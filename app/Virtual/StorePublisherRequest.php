<?php

/**
 * @OA\Schema(
 *      title="Store Publisher request",
 *      description="Store Publisher request body data",
 *      type="object",
 *      required={"name"}
 * )
 */

class StorePublisherRequest
{
    /**
     * @OA\Property(
     *      title="name",
     *      description="Name of the new publisher",
     *      example="A nice publisher"
     * )
     *
     * @var string
     */
    public $name;
}
