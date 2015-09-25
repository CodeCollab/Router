<?php declare(strict_types=1);
/**
 * Exception which gets thrown when trying to call a non existent action
 *
 * PHP version 7.0
 *
 * @category   CodeCollab
 * @package    Router
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2015 Pieter Hordijk <https://github.com/PeeHaa>
 * @license    See the LICENSE file
 * @version    1.0.0
 */
namespace CodeCollab\Router;

/**
 * Exception which gets thrown when trying to call a non existent action
 *
 * @category   CodeCollab
 * @package    Router
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class ActionNotFoundException extends \Exception
{
}
