<?php

namespace Framework\Controllers;
use Framework\Lib\AbstractController;

/**
 * Class NotFoundController
 *
 * @package Framework\Controllers
 *
 * If the user types unknown controller class in the url ie. www.x.com/unknown/
 * then the FrontController instantiates this class and call the method notFoundAction in the Abstract Controller.
 * So simply, this class and the notFoundAction at the Abstract Controller acts like a "404 not found"
 *
 * @author Shrief Mohamed
 */
class NotFoundController extends AbstractController
{

}