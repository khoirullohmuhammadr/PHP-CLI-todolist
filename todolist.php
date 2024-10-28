<?php

class TodoList
{
    private $filePrefix;
    private $taskLimit;

    public function __construct($filePrefix = 'todolist', $taskLimit = 30)
    {
        $this->filePrefix = $filePrefix;
        $this->taskLimit = $taskLimit;
    }

    private function getLatestFile()
    {
        $index = 1;
        while (file_exists($this->filePrefix . "_$index.json")) {
            $index++;
        }
        $lastFile = $this->filePrefix . "_" . ($index - 1) . ".json";
        if (file_exists($lastFile) && count($this->readTasks($lastFile)) < $this->taskLimit) {
            return $lastFile;
        }

        return $this->filePrefix . "_$index.json";
    }

    private function readTasks($filename = null)
    {
        if ($filename === null) {
            $filename = $this->getLatestFile();
        }

        if (!file_exists($filename)) {
            return [];
        }

        $jsonData = file_get_contents($filename);
        $tasks = json_decode($jsonData, true);

        return $tasks ?: [];
    }

    private function saveTasks($tasks, $filename = null)
    {
        if ($filename === null) {
            $filename = $this->getLatestFile();
        }

        $jsonData = json_encode($tasks, JSON_PRETTY_PRINT);
        file_put_contents($filename, $jsonData);
    }

    public function addTask()
    {
        $latestFile = $this->getLatestFile();
        $tasks = $this->readTasks($latestFile);

        if (count($tasks) >= $this->taskLimit) {

            $latestFile = $this->getLatestFile();
            $tasks = [];
        }

        echo "Input task title: ";
        $title = trim(fgets(STDIN));

        echo "Input task description: ";
        $description = trim(fgets(STDIN));

        $tasks[] = [
            'title' => $title,
            'description' => $description,
        ];

        $this->saveTasks($tasks, $latestFile);
        echo "Task '$title' Successfully added to '$latestFile'!" . PHP_EOL;
        sleep(2);
        $this->clearScreen();
    }

    public function listTasks()
    {
        $index = 1;
        $allTasks = [];

        while (file_exists($this->filePrefix . "_$index.json")) {
            $tasks = $this->readTasks($this->filePrefix . "_$index.json");
            $allTasks = array_merge($allTasks, $tasks);
            $index++;
        }

        if (empty($allTasks)) {
            echo "There's no task." . PHP_EOL;
        } else {
            foreach ($allTasks as $i => $task) {
                echo ($i + 1) . ". " . $task['title'] . " " . PHP_EOL;
            }
        }
        echo PHP_EOL . "Click enter to back into menu...";
        fgets(STDIN);

        $this->clearScreen();
    }

    public function viewTaskDetail()
    {
        $index = 1;
        $allTasks = [];

        while (file_exists($this->filePrefix . "_$index.json")) {
            $tasks = $this->readTasks($this->filePrefix . "_$index.json");
            $allTasks = array_merge($allTasks, $tasks);
            $index++;
        }

        echo "Masukkan nomor task untuk melihat detail: ";
        $taskNumber = (int) trim(fgets(STDIN));

        if (isset($allTasks[$taskNumber - 1])) {
            $task = $allTasks[$taskNumber - 1];
            echo "Judul: " . $task['title'] . PHP_EOL;
            echo "Deskripsi: " . $task['description'] . PHP_EOL;
        } else {
            echo "Task tidak ditemukan." . PHP_EOL;
        }

        echo PHP_EOL . "Tekan Enter untuk kembali ke menu...";
        fgets(STDIN);

        $this->clearScreen();
    }

    public function clearScreen()
    {
        echo chr(27) . "[2J" . chr(27) . "[;H";
    }
}


function main()
{
    $todo = new TodoList();

    while (true) {
        $todo->clearScreen();
        echo "Todo List:  " . PHP_EOL;
        echo "MENU:  ". PHP_EOL;
        echo "1. ADD Todo" . PHP_EOL;
        echo "2. LIST Todo" . PHP_EOL;
        echo "3. DETAIL Todo" . PHP_EOL;
        echo "4. EDIT Todo" . PHP_EOL;
        echo "5. DELETE Todo" . PHP_EOL;
        echo "6. Left" . PHP_EOL;
        echo "Pick option: ";
        $choice = trim(fgets(STDIN));

        switch ($choice) {
            case '1':
                $todo->addTask();
                break;
            case '2':
                $todo->listTasks();
                break;
            case '3':
                $todo->viewTaskDetail();
                break;
            case '4':
                $todo->editTask();
                break;
            case '5':
                $todo->deleteTask();
                break;
            case '6':
                echo "Left program." . PHP_EOL;
                exit(0);
            default:
                echo "Invalid option." . PHP_EOL;
                sleep(2);
                $todo->clearScreen();
                break;
        }
    }
}

main();
