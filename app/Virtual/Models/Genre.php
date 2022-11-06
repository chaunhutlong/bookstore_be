<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="Genre",
 *     description="Genre model",
 * )
 */
class Genre
{
  /**
   * @OA\Property(
   *     title="ID",
   *     description="ID",
   *     format="int64",
   *     example=1
   * )
   *
   * @var integer
   */
  public $id;

  /**
   * @OA\Property(
   *     title="Name",
   *     description="Name",
   * )
   *
   * @var string
   */
  public $name;

  /**
   * @OA\Property(
   *     title="Description",
   *    description="Description",
   * )
   * @var string
   */
  public $description;
}
