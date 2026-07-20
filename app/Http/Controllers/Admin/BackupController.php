<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function export()
    {
        if (auth()->user()->role !== 'SUPER_ADMIN') {
            abort(403, 'Unauthorized.');
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            $keyName = 'name';
        } else {
            $dbName = DB::connection()->getDatabaseName();
            $tables = DB::select('SHOW TABLES');
            $keyName = 'Tables_in_' . $dbName;
        }

        $response = new StreamedResponse(function () use ($tables, $keyName, $driver) {
            $handle = fopen('php://output', 'w');
            $pdo = DB::getPdo();

            if ($driver === 'sqlite') {
                fwrite($handle, "PRAGMA foreign_keys = OFF;\n\n");
            } else {
                fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");
            }

            foreach ($tables as $tableObj) {
                if (!isset($tableObj->$keyName)) {
                    continue;
                }
                $tableName = $tableObj->$keyName;

                if ($driver === 'sqlite') {
                    $createSqlObj = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$tableName])[0] ?? null;
                    $createSql = $createSqlObj ? $createSqlObj->sql : null;
                } else {
                    $createTableQuery = DB::select("SHOW CREATE TABLE `$tableName`")[0] ?? null;
                    $createSql = $createTableQuery ? ($createTableQuery->{'Create Table'} ?? $createTableQuery->{'Create View'} ?? null) : null;
                }
                
                if ($createSql) {
                    fwrite($handle, "DROP TABLE IF EXISTS `$tableName`;\n");
                    fwrite($handle, $createSql . ";\n\n");
                }

                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    fwrite($handle, "/* Data for table `$tableName` */\n");
                    
                    foreach ($rows as $row) {
                        $rowArray = (array)$row;
                        $columns = array_keys($rowArray);
                        $escapedColumns = array_map(function($col) {
                            return "`$col`";
                        }, $columns);
                        
                        $values = array_map(function($val) use ($pdo) {
                            if (is_null($val)) {
                                return 'NULL';
                            }
                            return $pdo->quote($val);
                        }, array_values($rowArray));
                        
                        $sql = "INSERT INTO `$tableName` (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        fwrite($handle, $sql);
                    }
                    fwrite($handle, "\n");
                }
            }

            if ($driver === 'sqlite') {
                fwrite($handle, "PRAGMA foreign_keys = ON;\n");
            } else {
                fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
            }
            fclose($handle);
        });

        $filename = 'backup_database_' . date('Y_m_d_His') . '.sql';

        $response->headers->set('Content-Type', 'application/sql');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
