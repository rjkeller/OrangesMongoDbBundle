<?php
namespace Oranges\MongoDbBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Oranges\MongoDbBundle\Helper\MongoDb;

/**
 * Provides the means from which you can view a GridFS image stored in MongoDB.
 */
class ShowImageController extends Controller
{
	public function showImageAction($database, $id)
	{
		$mid = new \MongoId($id);
		$mongo = MongoDb::getMongo();
		$db = $mongo->selectDB($database);

		$grid = $db->getGridFS();

		header("Content-Type: image/png");
		$file = $grid->get($mid);
		$stream = $file->getResource();
		while (!feof($stream))
			echo fread($stream, 8192);
		die();
	}
}
