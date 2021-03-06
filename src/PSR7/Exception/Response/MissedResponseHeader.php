<?php
/**
 * @author Dmitry Lezhnev <lezhnev.work@gmail.com>
 * Date: 02 May 2019
 */
declare(strict_types=1);


namespace OpenAPIValidation\PSR7\Exception\Response;


use OpenAPIValidation\PSR7\ResponseAddress;

class MissedResponseHeader extends \RuntimeException
{
    /** @var string */
    protected $headerName;
    /** @var ResponseAddress */
    protected $addr;

    static function fromResponseAddr(string $headerName, ResponseAddress $address): self
    {
        $i = new self(
            sprintf("Response header '%s' at [%s,%s,%d] not found",
                $headerName,
                $address->path(),
                $address->method(),
                $address->responseCode()
            )
        );

        $i->headerName = $headerName;
        $i->addr       = $address;
        return $i;
    }

    /**
     * @return string
     */
    public function headerName(): string
    {
        return $this->headerName;
    }

    /**
     * @return ResponseAddress
     */
    public function addr(): ResponseAddress
    {
        return $this->addr;
    }


}