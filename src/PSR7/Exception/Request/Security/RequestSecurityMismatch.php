<?php
/**
 * @author Dmitry Lezhnev <lezhnev.work@gmail.com>
 * Date: 07 May 2019
 */
declare(strict_types=1);


namespace OpenAPIValidation\PSR7\Exception\Request\Security;


use OpenAPIValidation\PSR7\OperationAddress;

class RequestSecurityMismatch extends \RuntimeException
{
    /** @var OperationAddress */
    protected $addr;

    static function fromOperationAddr(OperationAddress $address, \Throwable $prev = null): self
    {
        $i = new self(
            sprintf("Request does not match security schemes [%s,%s]",
                $address->path(),
                $address->method()
            ),
            0,
            $prev
        );

        $i->addr = $address;
        return $i;
    }

    /**
     * @return OperationAddress
     */
    public function addr(): OperationAddress
    {
        return $this->addr;
    }


}