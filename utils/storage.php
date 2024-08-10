<?php

abstract class File {
    protected $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        
        if (!file_exists($filePath))
            file_put_contents($filePath, '{}');
    }
}

class JsonFile extends File {
    public function getDataFromJson() {
        $jsonFile = file_get_contents($this->filePath);
        
        $data = json_decode($jsonFile, true);

        if (!is_array($data))
            throw new Exception("data exception: Invalid data type!");

        return $data;
    }

    public function putDataInJson($data) {
        $updatedData = json_encode($data, JSON_PRETTY_PRINT);
        
        //echo "<pre>";
        //print_r($updatedData);
        //echo "</pre>";

        file_put_contents($this->filePath, $updatedData);
    }
}