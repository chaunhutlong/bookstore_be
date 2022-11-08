<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="Discount",
 *     description="Discount model",
 * )
 */
class Discount
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
   *      example="A nice discount",
   *      type="string"
   * )
   *
   * @var string
   */
  public $name;


  /**
   * @OA\Property(
   *     title="Value",
   *     description="Value",
   *     type="number",
   * )
   * @var float
   */
  public $value;

  /**
   * @OA\Property(
   *     title="start_date",
   *     description="start_date",
   *     example="2020-01-27 17:50:45",
   *    format="datetime",
   *    type="string",
   * )
   * @var \DateTime
   */
  public $start_date;

  /**
   * @OA\Property(
   *     title="end_date",
   *     description="end_date of event",
   *     example="2020-01-27 17:50:45",
   *    format="datetime",
   *    type="string",
   * )
   * @var \DateTime
   */
  public $end_date;

  /**
   * @OA\Property(
   *     title="quantity",
   *     description="quantity of discount",
   *     example=1,
   *     type="integer",
   * )
   * @var integer
   */
  public $quantity;

  /**
   * @OA\Property(
   *      title="Description",
   *      description="Description",
   *      example="A nice description",
   *      type="string",
   * )
   * @var string
   */
  public $description;
}
