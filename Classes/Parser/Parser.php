<?php
namespace Neos\Fusion\Afx\Parser;

/*
 * This file is part of the Neos.Fusion.Afx package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

class Parser
{
    public function __construct($string)
    {
        $this->lexer = new Lexer($string);
    }

    public function parse()
    {
        return Expression\NodeList::parse($this->lexer);
    }
}
