<?php
declare(strict_types=1);
namespace Davitec\AbDownloads\Updates;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use Doctrine\DBAL\DBALException;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Upgrade wizard which goes through all files referenced in backend_layout.icon
 * and creates sys_file records as well as sys_file_reference records for each hit.
 */
class ImageUpdate extends AbstractUpdate
{
    /**
     * @var string
     */
    protected $title = 'Migrate all file relations from backend_layout.icon to sys_file_references';

    /**
     * @var ResourceStorage
     */
    protected $storage;

    /**
     * @var Logger
     */
    protected $logger;

    protected $migrate = [
        'tx_abdownloads_download' => [
            'image' => 'uploads/tx_abdownloads/downloadImages',
            'file' => 'uploads/tx_abdownloads/files',
        ],
        'tx_abdownloads_category' => [
            'image' => 'uploads/tx_abdownloads/categoryImages',
        ],
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    /**
     * Checks if an update is needed
     *
     * @param string &$description The description for the update
     *
     * @return bool TRUE if an update is needed, FALSE otherwise
     */
    public function checkForUpdate(&$description)
    {
        if ($this->isWizardDone()) {
            return false;
        }

        // If there are no valid records, the wizard can be marked as done directly
        $dbQueries = [];
/*        $records = $this->getRecordsFromTable($dbQueries);
        if (empty($records)) {
            $this->markWizardAsDone();
            return false;
        }*/

        $description = 'This update wizard goes through all files that are referenced '
            . ' and adds the files to the FAL File Index.<br />'
            . 'It also moves the files from uploads/... to the fileadmin/uploads/... path.';

        return true;
    }

    /**
     * Performs the database update.
     *
     * @param array &$dbQueries Queries done in this update
     * @param string &$customMessage Custom message
     * @return bool TRUE on success, FALSE on error
     */
    public function performUpdate(array &$dbQueries, &$customMessage)
    {
        $customMessage = '';
        foreach ($this->migrate as $table => $fieldsToMigrate) {
            foreach ($fieldsToMigrate as $field => $sourcePath) {
                $this->performUpdateForTableAndField($table, $field, $sourcePath, $customMessage);
            }
        }
        return empty($customMessage);
    }

    public function performUpdateForTableAndField($table, $field, $sourcePath, &$customMessage)
    {
        try {
            $storages = GeneralUtility::makeInstance(StorageRepository::class)->findAll();
            $this->storage = $storages[0];

            $records = $this->getRecordsFromTable($table, $field, $dbQueries);
            foreach ($records as $record) {
                $this->migrateField($record, $table, $field, $sourcePath, $customMessage, $dbQueries);
            }

            $this->markWizardAsDone();
        } catch (\Exception $e) {
            $customMessage .= PHP_EOL . $e->getMessage();
        }


    }

    /**
     * Get records from table where the field to migrate is not empty (NOT NULL and != '')
     * and also not numeric (which means that it is migrated)
     *
     * @param array $dbQueries
     *
     * @return array
     * @throws \RuntimeException
     */
    protected function getRecordsFromTable($table, $field, &$dbQueries)
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();

        try {
            $result = $queryBuilder
                ->select('uid', 'pid', $field)
                ->from($table)
                ->where(
                    $queryBuilder->expr()->isNotNull($field),
                    $queryBuilder->expr()->neq(
                        $field,
                        $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)
                    ),
                    $queryBuilder->expr()->comparison(
                        'CAST(CAST(' . $queryBuilder->quoteIdentifier($field) . ' AS DECIMAL) AS CHAR)',
                        ExpressionBuilder::NEQ,
                        'CAST(' . $queryBuilder->quoteIdentifier($field) . ' AS CHAR)'
                    )
                )
                ->orderBy('uid')
                ->execute();

            $dbQueries[] = $queryBuilder->getSQL();

            return $result->fetchAll();
        } catch (DBALException $e) {
            throw new \RuntimeException(
                'Database query failed. Error was: ' . $e->getPrevious()->getMessage(),
                1511950673
            );
        }
    }

    /**
     * Migrates a single field.
     *
     * @param array $row
     * @param string $customMessage
     * @param array $dbQueries
     *
     * @throws \Exception
     */
    protected function migrateField($row, $table, $field, $sourcePath, &$customMessage, &$dbQueries)
    {
        $targetPath = $sourcePath;
        $fieldItems = GeneralUtility::trimExplode(',', $row[$field], true);
        if (empty($fieldItems) || is_numeric($row[$field])) {
            return;
        }
        $fileadminDirectory = rtrim($GLOBALS['TYPO3_CONF_VARS']['BE']['fileadminDir'], '/') . '/';
        $i = 0;

        $storageUid = (int)$this->storage->getUid();
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        foreach ($fieldItems as $item) {
            $fileUid = null;
            $sourcePath = PATH_site . $sourcePath . $item;
            $targetDirectory = PATH_site . $fileadminDirectory . $targetPath;
            $targetPath = $targetDirectory . basename($item);

            // maybe the file was already moved, so check if the original file still exists
            if (file_exists($sourcePath)) {
                if (!is_dir($targetDirectory)) {
                    GeneralUtility::mkdir_deep($targetDirectory);
                }

                // see if the file already exists in the storage
                $fileSha1 = sha1_file($sourcePath);

                $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file');
                $queryBuilder->getRestrictions()->removeAll();
                $existingFileRecord = $queryBuilder->select('uid')->from('sys_file')->where(
                    $queryBuilder->expr()->eq(
                        'sha1',
                        $queryBuilder->createNamedParameter($fileSha1, \PDO::PARAM_STR)
                    ),
                    $queryBuilder->expr()->eq(
                        'storage',
                        $queryBuilder->createNamedParameter($storageUid, \PDO::PARAM_INT)
                    )
                )->execute()->fetch();

                // the file exists, the file does not have to be moved again
                if (is_array($existingFileRecord)) {
                    $fileUid = $existingFileRecord['uid'];
                } else {
                    // just move the file (no duplicate)
                    rename($sourcePath, $targetPath);
                }
            }

            if ($fileUid === null) {
                // get the File object if it hasn't been fetched before
                try {
                    // if the source file does not exist, we should just continue, but leave a message in the docs;
                    // ideally, the user would be informed after the update as well.
                    /** @var File $file */
                    $file = $this->storage->getFile($targetPath . $item);
                    $fileUid = $file->getUid();
                } catch (\InvalidArgumentException $e) {

                    // no file found, no reference can be set
                    $this->logger->notice(
                        'File ' . $sourcePath . $item . ' does not exist. Reference was not migrated.',
                        [
                            'table' => $table,
                            'record' => $row,
                            'field' => $field,
                        ]
                    );

                    $format = 'File \'%s\' does not exist. Referencing field: %s.%d.%s. The reference was not migrated.';
                    $message = sprintf(
                        $format,
                        $sourcePath . $item,
                        $table,
                        $row['uid'],
                        $field
                    );
                    $customMessage .= PHP_EOL . $message;
                    continue;
                }
            }

            if ($fileUid > 0) {
                $fields = [
                    'fieldname' => $field,
                    'table_local' => 'sys_file',
                    'pid' => ($table === 'pages' ? $row['uid'] : $row['pid']),
                    'uid_foreign' => $row['uid'],
                    'uid_local' => $fileUid,
                    'tablenames' => $table,
                    'crdate' => time(),
                    'tstamp' => time(),
                    'sorting' => ($i + 256),
                    'sorting_foreign' => $i,
                ];

                $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file_reference');
                $queryBuilder->insert('sys_file_reference')->values($fields)->execute();
                $dbQueries[] = str_replace(LF, ' ', $queryBuilder->getSQL());
                ++$i;
            }
        }

        // Update referencing table's original field to now contain the count of references,
        // but only if all new references could be set
        if ($i === count($fieldItems)) {
            $queryBuilder = $connectionPool->getQueryBuilderForTable($table);
            $queryBuilder->update($table)->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($row['uid'], \PDO::PARAM_INT)
                )
            )->set($field, $i)->execute();
            $dbQueries[] = str_replace(LF, ' ', $queryBuilder->getSQL());
        }
    }
}
