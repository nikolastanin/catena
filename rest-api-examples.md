# Slots REST API Documentation

## Endpoints

### Get All Slots
**URL:** `/wp-json/slots/v1/slots`

**Method:** GET

**Parameters:**
- `per_page` (optional): Number of slots per page (default: 10, max: 100)
- `page` (optional): Page number (default: 1)
- `provider` (optional): Filter by provider name
- `min_rating` (optional): Minimum star rating (0-5)
- `max_rating` (optional): Maximum star rating (0-5)
- `orderby` (optional): Sort by (date, title, rating, provider, rtp)
- `order` (optional): Sort order (ASC, DESC)

**Example Requests:**
```
GET /wp-json/slots/v1/slots
GET /wp-json/slots/v1/slots?per_page=20&page=2
GET /wp-json/slots/v1/slots?provider=NetEnt&min_rating=4
GET /wp-json/slots/v1/slots?orderby=rating&order=DESC
GET /wp-json/slots/v1/slots?min_rating=4.5&max_rating=5
```

### Get Single Slot
**URL:** `/wp-json/slots/v1/slots/{id}`

**Method:** GET

**Parameters:**
- `id`: Slot post ID

**Example Request:**
```
GET /wp-json/slots/v1/slots/123
```

## Response Format

### Slots List Response
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "title": "Starburst",
      "description": "A classic slot game with expanding wilds...",
      "content": "Full content here...",
      "slug": "starburst",
      "date": "2024-01-15T10:30:00+00:00",
      "modified": "2024-01-15T10:30:00+00:00",
      "status": "publish",
      "featured_image": {
        "id": 456,
        "url": "https://example.com/wp-content/uploads/starburst.jpg",
        "width": 800,
        "height": 600,
        "alt": "Starburst Slot Game"
      },
      "meta": {
        "star_rating": 4.5,
        "provider_name": "NetEnt",
        "rtp": 96.1,
        "min_wager": 0.10,
        "max_wager": 100.00
      },
      "links": {
        "self": "https://example.com/wp-json/slots/v1/slots/123",
        "collection": "https://example.com/wp-json/slots/v1/slots"
      }
    }
  ],
  "total": 25,
  "total_pages": 3,
  "current_page": 1,
  "per_page": 10
}
```

### Single Slot Response
```json
{
  "success": true,
  "data": {
    "id": 123,
    "title": "Starburst",
    "description": "A classic slot game with expanding wilds...",
    "content": "Full content here...",
    "slug": "starburst",
    "date": "2024-01-15T10:30:00+00:00",
    "modified": "2024-01-15T10:30:00+00:00",
    "status": "publish",
    "featured_image": {
      "id": 456,
      "url": "https://example.com/wp-content/uploads/starburst.jpg",
      "width": 800,
      "height": 600,
      "alt": "Starburst Slot Game"
    },
    "meta": {
      "star_rating": 4.5,
      "provider_name": "NetEnt",
      "rtp": 96.1,
      "min_wager": 0.10,
      "max_wager": 100.00
    },
    "links": {
      "self": "https://example.com/wp-json/slots/v1/slots/123",
      "collection": "https://example.com/wp-json/slots/v1/slots"
    }
  }
}
```

## JavaScript Usage Examples

### Fetch All Slots
```javascript
fetch('/wp-json/slots/v1/slots')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      data.data.forEach(slot => {
        console.log(`Slot: ${slot.title} by ${slot.meta.provider_name}`);
        console.log(`Rating: ${slot.meta.star_rating} stars`);
        console.log(`RTP: ${slot.meta.rtp}%`);
      });
    }
  });
```

### Fetch Slots with Filters
```javascript
const params = new URLSearchParams({
  provider: 'NetEnt',
  min_rating: 4,
  orderby: 'rating',
  order: 'DESC'
});

fetch(`/wp-json/slots/v1/slots?${params}`)
  .then(response => response.json())
  .then(data => {
    console.log(`Found ${data.total} slots`);
    data.data.forEach(slot => {
      console.log(`${slot.title}: ${slot.meta.star_rating} stars`);
    });
  });
```

### Fetch Single Slot
```javascript
fetch('/wp-json/slots/v1/slots/123')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const slot = data.data;
      console.log(`Slot: ${slot.title}`);
      console.log(`Provider: ${slot.meta.provider_name}`);
      console.log(`RTP: ${slot.meta.rtp}%`);
      console.log(`Wager: $${slot.meta.min_wager} - $${slot.meta.max_wager}`);
    }
  });
```

## PHP Usage Examples

### Get Slots in PHP
```php
// Get all slots
$response = wp_remote_get(home_url('/wp-json/slots/v1/slots'));
if (!is_wp_error($response)) {
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if ($data['success']) {
        foreach ($data['data'] as $slot) {
            echo "Slot: " . $slot['title'] . "\n";
            echo "Provider: " . $slot['meta']['provider_name'] . "\n";
            echo "Rating: " . $slot['meta']['star_rating'] . " stars\n";
        }
    }
}

// Get filtered slots
$args = array(
    'provider' => 'NetEnt',
    'min_rating' => 4,
    'orderby' => 'rating',
    'order' => 'DESC'
);
$url = add_query_arg($args, home_url('/wp-json/slots/v1/slots'));
$response = wp_remote_get($url);
```

## Error Responses

### Slot Not Found
```json
{
  "code": "slot_not_found",
  "message": "Slot not found",
  "data": {
    "status": 404
  }
}
```

### Invalid Parameters
```json
{
  "code": "rest_invalid_param",
  "message": "Invalid parameter(s): per_page",
  "data": {
    "params": {
      "per_page": "Invalid parameter."
    }
  }
}
```

## Notes

- All endpoints are public (no authentication required)
- Featured images include full image data (URL, dimensions, alt text)
- Custom fields are included in the `meta` object
- Pagination is supported with `per_page` and `page` parameters
- Filtering by provider and rating ranges is supported
- Sorting by various fields including custom meta fields is supported
- All dates are returned in ISO 8601 format
- Links include self-reference and collection URLs for navigation
