<?php
/*
 * This file is part of the Meriel package.
 *
 * (c) Stefano Anedda <dearste@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meriel\View\Engine;


interface EngineInterface
{

    public function getEngine();

    public function render($template, $data);

}