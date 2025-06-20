# Error Handling System

## Overview

The application now uses a modern HTTP exception-based error handling system that replaces the legacy `error.php` with GET parameters. The new system provides better error messages, proper HTTP status codes, and a unified error display page.

## Key Components

### HttpException Class (`exceptions/HttpException.php`)
- Custom exception class that encapsulates HTTP status codes, status text, and error messages
- Automatically generates appropriate default messages based on status codes
- Supports common HTTP status codes (400, 401, 403, 404, 409, 422, 500, etc.)

### Dynamic Error Page (`views/errors/error.php`)
- Single unified error page that displays different content based on status code
- Replaces the separate 404.php and 500.php files
- Responsive design with appropriate action buttons based on error type

### ErrorController Updates
- New `handleError()` method for displaying errors with custom status codes and messages
- New `handleHttpException()` method for processing HttpException objects
- Backward compatibility with existing `notFound()` and `serverError()` methods

### BaseController Updates
- Global exception handling is managed by the FrontController
- Controllers simply throw HttpExceptions directly
- No need for additional wrapper methods or complexity

### FrontController Updates
- Global exception handling at the routing level
- Catches both HttpException and generic Exception objects
- Provides fallback error handling for unexpected errors

## Usage

### Throwing HttpExceptions in Controllers

Controllers now simply throw HttpExceptions directly. The FrontController handles them globally:

```php
// Old way
if (!$id || !is_numeric($id)) {
    $this->redirect("/error.php?error=invalidParam");
}

// New way - simple and clean
if (!$id || !is_numeric($id)) {
    throw new HttpException(400, "The ID parameter is invalid or missing.");
}
```

### Exception Handling in Database Operations

For database operations, wrap in try-catch and convert to HttpExceptions:

```php
try {
    $artwork = $this->artworkRepository->findById($artworkId);
    
    if (!$artwork) {
        throw new HttpException(404, "No artwork with the given ID was found.");
    }
    
    // Continue with normal logic...
    
} catch (HttpException $e) {
    throw $e; // Re-throw HttpExceptions
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    throw new HttpException(500, "A database error occurred. Please try again later.");
}
```

### Common HttpException Examples

```php
// Bad Request (400)
throw new HttpException(400, "Invalid input data provided.");

// Unauthorized (401)
throw new HttpException(401, "You must be logged in to access this resource.");

// Forbidden (403)
throw new HttpException(403, "You don't have permission to perform this action.");

// Not Found (404)
throw new HttpException(404, "The requested artwork was not found.");

// Conflict (409)
throw new HttpException(409, "You have already reviewed this artwork.");

// Unprocessable Entity (422)
throw new HttpException(422, "The submitted data is invalid.");

// Internal Server Error (500)
throw new HttpException(500, "A database error occurred. Please try again later.");
```

The system is designed to be simple and straightforward - just throw the exception and let the FrontController handle the rest!

## Migration from Legacy System

### Controller Updates
All controllers have been updated to use the new system:
- `ArtworkController::show()`
- `ArtistController::show()`
- `GenreController::show()`
- `SubjectController::show()`
- `ReviewController::addReview()`
- `ReviewController::deleteReview()`
- `AuthController::showAccount()`

### Legacy Support
The legacy `error.php` file still exists for backward compatibility but now internally uses the new error handling system. This ensures that any remaining legacy redirects will still work correctly.

### Error Code Mappings
Legacy error codes are automatically mapped to appropriate HTTP status codes:

| Legacy Code | HTTP Status | Description |
|-------------|-------------|-------------|
| invalidParam | 400 | Bad Request |
| notLoggedIn | 401 | Unauthorized |
| unauthorized | 403 | Forbidden |
| *NotFound | 404 | Not Found |
| duplicateReview | 409 | Conflict |
| invalidReviewData | 422 | Unprocessable Entity |
| databaseError | 500 | Internal Server Error |

## Benefits

1. **Better User Experience**: More informative error messages with appropriate styling
2. **Proper HTTP Status Codes**: Search engines and APIs receive correct status codes
3. **Maintainable Code**: Centralized error handling reduces code duplication
4. **Type Safety**: HttpException provides better error context than string-based redirects
5. **Consistent Styling**: Single error page ensures consistent look and feel
6. **Mobile Responsive**: Error pages adapt to different screen sizes

## File Structure

```
exceptions/
  HttpException.php          # Custom HTTP exception class

views/errors/
  error.php                  # Dynamic error page (new)
  404.php                    # Legacy 404 page (deprecated)
  500.php                    # Legacy 500 page (deprecated)

controllers/
  ErrorController.php        # Updated with new methods
  BaseController.php         # Updated with exception handling
  *Controller.php            # Updated to use HttpExceptions

router/
  FrontController.php        # Updated with global exception handling

error.php                    # Legacy error handler (backward compatibility)
```

## Future Improvements

1. **Error Logging**: Enhanced error logging with context information
2. **Error Reporting**: Admin dashboard for monitoring application errors
3. **Custom Error Pages**: Theme-specific error pages for different sections
4. **API Error Format**: JSON error responses for API endpoints
5. **Error Analytics**: Track common errors to improve user experience
