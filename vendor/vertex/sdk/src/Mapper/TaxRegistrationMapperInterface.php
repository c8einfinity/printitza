<?php
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

namespace Vertex\Mapper;

use Vertex\Data\TaxRegistrationInterface;
use Vertex\Exception\ValidationException;

/**
 * SOAP mapping methods for {@see TaxRegistrationInterface}
 *
 * @api
 */
interface TaxRegistrationMapperInterface
{
    /**
     * Turn a SOAP response object into an instance of {@see TaxRegistrationInterface}
     *
     * @param \stdClass $map
     * @return TaxRegistrationInterface
     */
    public function build(\stdClass $map);

    /**
     * Turn an instance of {@see TaxRegistrationInterface} into a SOAP compatible format
     *
     * @param TaxRegistrationInterface $object
     * @return \stdClass
     * @throws ValidationException
     */
    public function map(TaxRegistrationInterface $object);
}
