# MCC Api Tools / Request Object Bundle

**Request Object Bundle** is a Symfony bundle designed to simplify handling HTTP requests by automatically transforming request parameters into objects and validating them.

## Requirements

- PHP 8.1 or higher (with `php-json` extension)
- Symfony 6.2 or newer

## Installation

Run the following command to install the package via Composer:

```bash
composer require mcc-api-tools/request-object-bundle
```

## Configuration

1. **Add the package to your Symfony project** (if not already added):

   ```php
   // config/bundles.php

   return [
       // ...
       MccApiTools\RequestObjectBundle\RequestObjectBundle::class => ['all' => true],
   ];
   ```

2. **Create a request object** that will represent your HTTP request data:

   ```php
   // src/Request/CreateLocationRequest.php

   declare(strict_types=1);

   namespace App\Request;

   use MccApiTools\RequestObjectBundle\Model\AllowExtraAttributesInterface;
   use MccApiTools\RequestObjectBundle\Model\RequestObjectInterface;
   use Symfony\Component\Validator\Constraints as Assert;

   class CreateLocationRequest implements RequestObjectInterface, AllowExtraAttributesInterface
   {
       #[Assert\NotBlank(message: "The location name is required")]
       public string $name;

       #[Assert\NotBlank(message: "The location address is required")]
       public string $address;

       #[Assert\NotBlank(message: "The latitude is required")]
       #[Assert\Type(type: "float", message: "The latitude must be a number")]
       public float $latitude;

       #[Assert\NotBlank(message: "The longitude is required")]
       #[Assert\Type(type: "float", message: "The longitude must be a number")]
       public float $longitude;
   }
   ```

3. **Use the request object in your controller**:

   ```php
   // src/Controller/LocationController.php

   declare(strict_types=1);

   namespace App\Controller;

   use App\Request\CreateLocationRequest;
   use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
   use Symfony\Component\HttpFoundation\Response;
   use Symfony\Component\Routing\Attribute\Route;

   class LocationController extends AbstractController
   {
       #[Route("/locations", methods: ["POST"])]
       public function create(CreateLocationRequest $request): Response
       {
           // Access request data via $request->name, $request->address, $request->latitude, $request->longitude

           // Logic for creating a location

           return new Response(null, 204);
       }
   }
   ```

## Usage

- **Validation**: Request objects are automatically validated based on the constraints defined in their properties. If validation fails, a response with validation errors will be returned.

- **Accessing Data**: You can access request data directly through the request object properties in your controller.

## Testing

To run tests, use the following command:

```bash
composer test
```

## Contributing

If you would like to contribute to this package, please open an issue or submit a pull request in the GitHub repository.

## License

This project is licensed under the MIT License. See the LICENSE file for details.
