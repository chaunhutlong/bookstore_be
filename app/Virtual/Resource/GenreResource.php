<?php

/**
 * @OA\Schema(
 * title="GenreResource",
 * description="Genre resource",
 * @OA\Xml(
 * name="GenreResource"
 * )
 * )
 */
class GenreResource
{
  /**
   * @OA\Property(
   * title="Data",
   * description="Data wrapper"
   * )
   *
   * @var \App\Virtual\Models\Genre[]
   */
  public $genre;
}
