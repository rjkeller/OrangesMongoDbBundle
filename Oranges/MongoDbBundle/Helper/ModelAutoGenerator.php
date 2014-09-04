<?php
namespace Oranges\MongoDbBundle\Helper;

interface ModelAutoGenerator
{
    private $__columns;

    public function __construct()
    {
        $data = get_object_vars($this);
		$columns = array();
		foreach ($data as $key => $value)
		{
			if (substr($key, 0, 2) == "__")
			{
				unset($data[$key]);
				continue;
			}
			$data[$key] = false;
			$columns[] = $key;
		}

        $this->__columns = $columns;
    }

    /**
     Returns a pretty name of the row with the specified name.
     */
    public abstract function getName($row);

    /**
     Returns whether or not we should print this field in the auto generator.
     */
    public abstract function printField($row);

    public function getFields()
    {
        return $this->__columns;
    }

    public function validate()
    {
		$errorList = MasterContainer::get("validator")->validate($this);

		$hasError = false;
		foreach ($errorList as $error)
		{
			MessageBoxHandler::error($error->getMessage());
			$hasError = true;
		}
		return $hasError;
    }
}
