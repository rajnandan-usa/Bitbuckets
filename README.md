# Bitbuckets
rest api endpoints to performe CRUD operation on wp_posts table. Use
token authentication to verify the api request. (Token must be as static value)

Add this code to a PHP file within your WordPress plugin directory, activate the plugin. Then, you can use the following URLs in Postman to test the endpoints:
# Endpoints:-
1. Fetch All Posts:
URL: http://localhost/wordpress/wp-json/wp_api/v1/posts
Method: GET
Headers:
Key: Authorization, Value: 4ed31d41a135204be4
2. Fetch Specific Post by ID:
URL: http://localhost/wordpress/wp-json/wp_api/v1/post/1
Method: GET
Headers:
Key: Authorization, Value: 4ed31d41a135204be4
3. Paginate Posts:
URL: http://localhost/wordpress/wp-json/wp_api/v1/posts_pages/1
Method: GET
Headers:
Key: Authorization, Value: 4ed31d41a135204be4
4. Update Post:
URL: http://localhost/wordpress/wp-json/wp_api/v1/post/update
Method: POST
Headers:
Key: Authorization, Value: 4ed31d41a135204be4
Body: (Choose raw JSON format)
json:- 
{
    "id": 1,
    "post_title": "Updated Post Title",
    "post_type": "Page"
}


