<?php
namespace Oranges\MongoDbBundle\Form;

use Oranges\UserBundle\Helper\SessionManager;
use Oranges\forms\WgForm;
use Oranges\FrontendBundle\Helper\MessageBoxHandler;
use Oranges\errorHandling\UserErrorHandler;

use Oranges\FormsBundle\Validator\Constraints as FormAssert;
use Symfony\Component\Form\FormBuilder;

class NewEntityForm extends WgForm
{
	private $entity_name;

    public function __construct($entity)
    {
        $this->entity_name = $entity;
    }

	public function getName()
	{
		return "NewEntityForm";
	}

	public function submitForm()
	{
	    $entity = new $this->entity_name();
	    $entity->id = $_POST['id'];
	    $entity->validate($entity);
	    if (UserErrorHandler::hasErrors())
	        return;

        $entity->load($entity->id);
	    $entity->delete();
	}
}