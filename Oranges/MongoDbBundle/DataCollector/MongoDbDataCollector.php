<?php
namespace Oranges\MongoDbBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Oranges\MongoDbBundle\Helper\MongoDb;
use Oranges\MasterContainer;

class MongoDbDataCollector implements DataCollectorInterface
{
	private $data;

	private $totalMilliseconds;

	/**
	 * Collects data for the given Request and Response.
	 *
	 * @param Request	 $request	A Request instance
	 * @param Response	 $response	A Response instance
	 * @param \Exception $exception An Exception instance
	 */
	function collect(Request $request, Response $response, \Exception $exception = null)
	{
		$this->easyCollect();
	}

	public function easyCollect()
	{
		$db = MongoDb::getDatabase();
		$db->setProfilingLevel(0);
		$allData = $db->selectCollection("system.profile")->find(array());

		if ($this->data == null)
			$this->data = array();

		foreach ($allData as $d)
		{
			if (
				!(isset($d['command']) && isset($d['command']['profile']))
				)
			{
				$storeMe = array('time' => $d['millis']);

				if (isset($d['command']) && isset($d['command']['count']))
				{
					if (!isset($d['command']['query']))
						continue;
					$storeMe['table'] = $d['command']['count'];
					$storeMe['query'] = $d['command']['query'];
				}
				else if (isset($d['ns']) && isset($d['query']))
				{
					$storeMe['table'] = $d['ns'];
					$storeMe['query'] = $d['query'];

				}
				else
				{
					$storeMe['table'] = $d;
					$storeMe['query'] = $d;
				}

				array_push($this->data, $storeMe);
			}
			$this->totalMilliseconds += $d['millis'];
		}

		$db->selectCollection("system.profile")->drop();
		$db->setProfilingLevel(MasterContainer::getParameter("mongodb_profiler"));
	}

	public function getMillis()
	{
		return $this->totalMilliseconds;
	}

	public function getData()
	{
		$this->easyCollect();
		return $this->data;
	}

	public function printR($q)
	{
		print_r($q);
	}

	public function getNum()
	{
		return sizeof($this->data);
	}

	/**
	 * Returns the name of the collector.
	 *
	 * @return string The collector name
	 */
	function getName()
	{
		return "MongoDb";
	}
}