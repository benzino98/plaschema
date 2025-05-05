<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="PLASCHEMA API",
 *     version="1.0.0",
 *     description="API for the Plateau State Contributory Healthcare Management Agency",
 *     @OA\Contact(
 *         email="info@plaschema.gov.ng",
 *         name="PLASCHEMA Support"
 *     )
 * )
 * @OA\Server(
 *     url="/",
 *     description="Main server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer"
 * )
 * @OA\Schema(
 *     schema="News",
 *     title="News",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="slug", type="string"),
 *     @OA\Property(property="content", type="string"),
 *     @OA\Property(property="excerpt", type="string"),
 *     @OA\Property(property="featured", type="boolean"),
 *     @OA\Property(property="image", type="string", format="uri"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="slug", type="string")
 *     ),
 *     @OA\Property(property="published_at", type="string", format="date-time"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="HealthcareProvider",
 *     title="Healthcare Provider",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="slug", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="city", type="string"),
 *     @OA\Property(property="state", type="string"),
 *     @OA\Property(property="contact_info", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="specialties", type="string"),
 *     @OA\Property(property="is_active", type="boolean"),
 *     @OA\Property(property="is_featured", type="boolean"),
 *     @OA\Property(property="image", type="string", format="uri"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="slug", type="string")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="FAQ",
 *     title="Frequently Asked Question",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="question", type="string"),
 *     @OA\Property(property="answer", type="string"),
 *     @OA\Property(property="is_published", type="boolean"),
 *     @OA\Property(property="order", type="integer"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="slug", type="string")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *     schema="ContactMessage",
 *     title="Contact Message",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="subject", type="string"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="status", type="string", enum={"new", "read", "responded", "archived"}),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
