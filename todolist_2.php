<!-- ini menggunakan file json sebagai tempat menyimpan todo dengan maksimal 20 todo -->

<?php

class TodoList
{
    private $filePrefix;
    private $taskLimit;

    public function __construct($filePrefix = 'todolist', $taskLimit = 20)
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
        return json_decode($jsonData, true) ?: [];
    }

    private function saveTasks($tasks, $filename = null)
    {
        if ($filename === null) {
            $filename = $this->getLatestFile();
        }

        file_put_contents($filename, json_encode($tasks, JSON_PRETTY_PRINT));
    }

    public function addTask()
    {
        $latestFile = $this->getLatestFile();
        $tasks = $this->readTasks($latestFile);

        if (count($tasks) >= $this->taskLimit) {
            $latestFile = $this->filePrefix . "_" . ((int) filter_var($latestFile, FILTER_SANITIZE_NUMBER_INT) + 1) . ".json";
            $tasks = [];
        }

        echo "Input task title: ";
        $title = trim(fgets(STDIN));

        echo "Input task description: ";
        $description = trim(fgets(STDIN));

        $tasks[] = ['title' => $title, 'description' => $description];
        $this->saveTasks($tasks, $latestFile);
        echo "Task '$title' successfully added to '$latestFile'!" . PHP_EOL;
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
            echo "No tasks available." . PHP_EOL;
        } else {
            foreach ($allTasks as $i => $task) {
                echo ($i + 1) . ". " . $task['title'] . PHP_EOL;
            }
        }

        echo "\nPress Enter to return to the menu...";
        fgets(STDIN);
        $this->clearScreen();
    }

    public function clearScreen()
    {
        system(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'cls' : 'clear');
    }
}

function main()
{
    $todo = new TodoList();
    
    while (true) {
        $todo->clearScreen();
        echo "Todo List:\nMENU:\n";
        echo "1. ADD Todo\n";
        echo "2. LIST Todo\n";
        echo "3. Exit\n";
        echo "Pick an option: ";
        $choice = trim(fgets(STDIN));
        
        switch ($choice) {
            case '1':
                $todo->addTask();
                break;
            case '2':
                $todo->listTasks();
                break;
            case '3':
                echo "Exiting program." . PHP_EOL;
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
