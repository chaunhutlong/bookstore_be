<?php

/**
 * @OA\Schema(
 * title="PublisherResource",
 * description="Publisher resource",
 * @OA\Xml(
 * name="PublisherResource"
 * )
 * )
 */
class PublisherResource
{
  /**
   * @OA\Property(
   * title="Data",
   * description="Data wrapper"
   * )
   *
   * @var \App\Virtual\Models\Publisher[]
   */
  private $publisher;
}
