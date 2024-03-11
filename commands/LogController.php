<?php

namespace app\commands;

use app\models\LogsNginx;
use DateTimeZone;
use Yii;
use yii\console\Controller;
use yii\db\Exception;
use ZipArchive;

class LogController extends Controller
{

    public function actionNginx()
    {
        $this->unzipArchives();

        $dirPath = __DIR__ . '/../logs/nginx/processed';
        $files = scandir($dirPath);

        foreach($files as $file) {
            if(is_file($dirPath . '/' . $file)) {
                $this->parseLogFile($dirPath . '/' . $file);
            }
        }

    }

    function unzipArchives()
    {
        $zipFiles = glob(__DIR__ . '/../logs/nginx/*.zip');
        foreach ($zipFiles as $zipFile) {

            $zip = new ZipArchive;
            $extractPath = __DIR__ . '/../logs/nginx/processed';

            if ($zip->open($zipFile) === TRUE) {
                $zip->extractTo($extractPath);
                $zip->close();

                unlink($zipFile);
            }
        }
    }

    function parseLogFile(string $path)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $iterator = $this->readTheFile($path);

            foreach ($iterator as $iteration) {
                if (!$iteration) {
                    continue;
                }

                preg_match('/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}) - - \[(.*?)\] "(.*?)" (\d*) (\d*) "(.*?)" "(.*?)"/', $iteration, $matches);

                $userAgent = get_browser($matches[7], true);
                $log = new LogsNginx([
                    'ip' => $matches[1],
                    'date' => (new \DateTime($matches[2]))->setTimezone(new DateTimeZone('Europe/Moscow'))->format('Y-m-d H:i:s'),
                    'code' => $matches[4],
                    'size' => $matches[5],
                    'url' => $matches[3],
                    'page' => $matches[6],
                    'browser' => $userAgent['browser'],
                    'platform' => $userAgent['platform'],
                    'user_agent' => $matches[7],
                ]);

                if (!$log->save()) {
                    //Сделал пока что просто пропуск этих логов, при желании можно добавить ошибку, чтобы в дальнейшем проверять каждые ошибки
                    var_dump($log->errors);
                }
            }

            print $this->formatBytes(memory_get_peak_usage());
            $transaction->commit();
        } catch (Exception $exception) {
            var_dump($exception->getMessage());
            $transaction->rollback();
        }
    }

    function readTheFile($path): array
    {
        $lines = [];
        $handle = fopen($path, "r");

        while(!feof($handle)) {
            $lines[] = trim(fgets($handle));
        }

        fclose($handle);
        return $lines;
    }

    function formatBytes($bytes, $precision = 2): string
    {
        $units = array("b", "kb", "mb", "gb", "tb");

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . " " . $units[$pow];
    }
}
