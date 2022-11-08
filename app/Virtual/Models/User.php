<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="User",
 *     description="User model",
 * )
 */
class User
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
   *     title="Email",
   *     description="Email",
   *     format="email",
   * )
   *
   * @var string
   */
  public $email;

  /**
   * @OA\Property(
   *     title="Created at",
   *     description="Created at",
   *     example="2020-01-27 17:50:45",
   *     format="datetime",
   *     type="string"
   * )
   *
   * @var \DateTime
   */
  public $created_at;

  /**
   * @OA\Property(
   *     title="Updated at",
   *     description="Updated at",
   *     example="2020-01-27 17:50:45",
   *     format="datetime",
   *     type="string"
   * )
   *
   * @var \DateTime
   */
  public $updated_at;
}
