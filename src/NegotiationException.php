<?php
namespace Gofabian\Negotiation;

use Exception;
use RuntimeException;

class NegotiationException extends RuntimeException
{

    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

}
