<?php
require_once 'DataItem.php';

class DataReader
{
    /**
     * @var DataReader
     */
    private static $_userInstance;
    /**
     * @var DataReader
     */
    private static $_pageInstance;

    /**
     * @var SplFixedArray
     */
    private $data;
    /**
     * @var string
     */
    private $filename;

    /**
     * @var boolean
     */
    public $isEmpty;

    /**
     * @param string $filename the path to the json file.
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
        $json = json_decode(file_get_contents($filename), true);

        if (empty($json) && !is_array($json))
            $this->data = new SplFixedArray();
        else
        {
            $i = 0;
            $this->data = new SplFixedArray(count($json));
            foreach ($json as $row)
            {
                $this->data[$i] = new DataItem($row, $i);
                $i++;
            }
        }

        $this->isEmpty = ($this->count()==0);
    }

    /**
     * @return integer the length of the array
     */
    public function count()
    {
        return $this->data->count();
    }

    /**
     * Gets the user instance
     * @return DataReader
     */
    public static function getUsers()
    {
        if (is_null(self::$_userInstance))
            self::$_userInstance = new DataReader(__DIR__.DS.'..'.DS.'data'.DS.'users.json');

        return self::$_userInstance;
    }

    /**
     * Gets the page instance
     * @return DataReader
     */
    public static function getPages()
    {
        if (is_null(self::$_pageInstance))
            self::$_pageInstance = new DataReader(__DIR__.DS.'..'.DS.'data'.DS.'pages.json');

        return self::$_pageInstance;
    }

    /**
     * Finds the DataItem with the specified attribute and value.
     * @param string $attribute
     * @param mixed $value
     * @return DataItem|null
     */
    public function find($attribute, $value)
    {
        foreach ($this->data as $item)
            if ($item->match($attribute, $value))
                return $item;

        return null;
    }

    /**
     * Finds all the DataItems with the specified attribute and value
     * @param string $attribute
     * @param mixed $value
     * @return SplFixedArray
     */
    public function findAll($attribute, $value)
    {
        $tmp = new SplFixedArray();
        foreach ($this->data as $item)
        {
            if ($item->match($attribute, $value))
            {
                $tmp->setSize($tmp->count()+1);
                $tmp[]=$item;
            }
        }

        return $tmp;
    }

    /**
     * Adds the item to the list
     * @param DataItem $item
     */
    public function addItem(DataItem $item)
    {
        $this->data->setSize($this->count()+1);
        $item->setIndex($this->count()-1);
        $this->data[$item->getIndex()] = $item;
    }

    /**
     * Adds the item to the list
     * @param array $data item data as array
     */
    public function addFromArray(array $data)
    {
        $this->data->setSize($this->count()+1);
        $this->data[$this->count()-1] = new DataItem($data, $this->count());
    }

    /**
     * Remove an item.
     * @param string $attribute
     * @param mixed $value
     */
    public function removeByAttribute($attribute, $value)
    {
        $item = $this->find($attribute, $value);

        if (!is_null($item))
            unset($this->data[$item->getIndex()], $item);
    }

    /**
     * Remove an item
     * @param DataItem $item
     */
    public function removeByItem(DataItem $item)
    {
        unset($this->data[$item->getIndex()]);
    }

    /**
     * Remove the $i object
     * @param integer $i
     */
    public function removeByIndex($i)
    {
        unset($this->data[$i]);
    }

    /**
     * Update from array data
     * @param array $condition
     * @param array $data
     */
    public function updateFromArray(array $condition,array $data)
    {
        $item = $this->find($condition['attribute'], $condition['value']);

        if (!is_null($item))
        {
            foreach ($data as $attr => $val)
                $item->$attr = $val;

            $this->data[$item->getIndex()]=$item;
        }
    }

    /**
     * Update from item
     * @param DataItem $item
     */
    public function updateFromItem(DataItem $item)
    {
        $this->data[$item->getIndex()] = $item;
    }

    public function commit()
    {
        $tmp = array();
        foreach ($this->data as $item)
        {
            $tmp[] = $item->toJson();
        }

        file_put_contents($this->filename, '['.implode(',', $tmp).']');
    }

    /**
     * @return SplFixedArray the data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array the data
     */
    public function toArray()
    {
        return $this->data->toArray();
    }

    /**
     * @return string converts to json
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string the json representation of the object.
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
