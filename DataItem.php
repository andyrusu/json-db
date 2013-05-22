<?php

class DataItem
{
    /**
     * @var array
     */
    private $data;

    private $index = 0;

    /**
     * @param array $data
     * @param integer $index the item's position in the list.
     */
    public function __construct(array $data, $index=0)
    {
        $this->data = $data;
        $this->index = $index;
    }

    public function __set($attribute, $value)
    {
        $this->setAttribute($attribute, $value);
    }

    public function __get($attribute)
    {
        $this->getAttribute($attribute);
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex($i)
    {
        $this->index = $i;
    }

    public function setAttribute($attribute, $value)
    {
        $this->data[$attribute] = $value;
    }

    public function getAttribute($attribute)
    {
        return $this->data[$attribute];
    }

    public function match($attribute, $value)
    {
        return array_key_exists($attribute, $this->data)&&$this->data[$attribute]==$value;
    }

    public function isEqualToArray($data)
    {
        if (count($data)!=count($this->data))
            return false;

        foreach ($this->data as $i => $v)
            if ($this->match($i, $v))
                return false;

        return true;
    }

    public function isEqualToItem(DataItem $item)
    {
        return $item->isEqualToArray($this->data);
    }

    public function toArray()
    {
        return $this->data;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return $this->toJson();
    }
}
