<?php
namespace Oranges\MongoDbBundle\Helper;

use Oranges\framework\BuildOptions;
use Oranges\MasterContainer;

class MongoDb
{
	private static $inst = null;

	public static function getMongo()
	{
		$m = null;
		if (MasterContainer::hasParameter("mongodb_host"))
			$m = new \Mongo(MasterContainer::getParameter("mongodb_host"));
		else
			$m = new \Mongo();
		return $m;
	}


	public static function getDatabase()
	{
		if (self::$inst == null)
		{
			$m = self::getMongo();

			if (MasterContainer::hasParameter("mongodb_database"))
			{
				self::$inst = $m->selectDB(MasterContainer::getParameter("mongodb_database"));
			}
			else
			{
				self::$inst = $m->ajent;
			}
		}

		if (MasterContainer::getParameter("mongodb_profiler") > 0)
		{
			self::$inst->setProfilingLevel(MasterContainer::getParameter("mongodb_profiler"));
		}

		return self::$inst;
	}

	public static function modelQuery($query, $model)
	{
		return new ModelIterator($query, $model);
	}
}
