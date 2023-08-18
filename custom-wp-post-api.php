<?php
/**
 * Plugin Name: Custom WP Post API
 * Description: Custom REST API endpoints for CRUD operations on wp_posts table.
 * Version: 1.0
 * Author: Rajnandan Kushwaha
 */

class Custom_WP_Post_API {

    private $api_namespace = 'wp_api/v1';

    public function __construct() {
        add_action('rest_api_init', array($this, 'register_api_endpoints'));
    }
//APIs endpoints
    public function register_api_endpoints() {
        register_rest_route($this->api_namespace, '/posts', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_posts'),
        ));

        register_rest_route($this->api_namespace, '/post/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_post_by_id'),
        ));

        register_rest_route($this->api_namespace, '/posts_pages/(?P<page>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_paginated_posts'),
        ));

        register_rest_route($this->api_namespace, '/post/update', array(
            'methods' => 'POST',
            'callback' => array($this, 'update_post'),
        ));
    }
//get all post
    public function get_posts($request) {
        $response = $this->verify_token($request);
        if (is_wp_error($response)) {
            return $response;
        }

        $posts = get_posts();

        $response_data = array(
            'status' => 200,
            'reason' => 'Success',
            'data' => $posts,
        );

        return $response_data;
    }
//get post by id
    public function get_post_by_id($request) {
        $response = $this->verify_token($request);
        if (is_wp_error($response)) {
            return $response;
        }

        $id = $request['id'];
        $post = get_post($id);

        if (!$post) {
            return new WP_Error('invalid_post', 'Invalid post ID.', array('status' => 404));
        }

        $response_data = array(
            'status' => 200,
            'reason' => 'Success',
            'data' => $post,
        );

        return $response_data;
    }
//get post with pagination
    public function get_paginated_posts($request) {
        $response = $this->verify_token($request);
        if (is_wp_error($response)) {
            return $response;
        }

        $page = intval($request['page']);
        $per_page = 10;

        $args = array(
            'posts_per_page' => $per_page,
            'paged' => $page,
        );

        $posts = get_posts($args);

        $total_posts = wp_count_posts()->publish;
        $total_pages = ceil($total_posts / $per_page);

        $response_data = array(
            'status' => 200,
            'reason' => 'Success',
            'data' => array(
                'current_page' => $page,
                'total_pages' => $total_pages,
                'posts' => $posts,
            ),
        );

        return $response_data;
    }
//update post with id
    public function update_post($request) {
        $response = $this->verify_token($request);
        if (is_wp_error($response)) {
            return $response;
        }
    
        $data = json_decode($request->get_body());
    
        if (!$data || empty($data->id) || empty($data->post_title) || empty($data->post_type)) {
            return new WP_Error('invalid_data', 'Invalid data provided.', array('status' => 400));
        }
    
        $post_id = $data->id;
        $updated_post_title = sanitize_text_field($data->post_title);
        $updated_post_type = sanitize_text_field($data->post_type);
    
        $existing_post = get_post($post_id);
        if (!$existing_post) {
            return new WP_Error('post_not_found', 'Post not found or post type mismatch.', array('status' => 404));
        }
    
        $post = array(
            'ID' => $post_id,
            'post_type' => $updated_post_type,
            'post_title' => $updated_post_title,
        );
    
        wp_update_post($post);
    
        $updated_post = get_post($post_id);
    
        $response_data = array(
            'status' => 200,
            'reason' => 'Success',
            'data' => $updated_post,
        );
    
        return $response_data;
    }
//verify authorization token
    private function verify_token($request) {
        $token = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

        if ($token !== '4ed31d41a135204be4') {
            return new WP_Error('unauthorized', 'Unauthorized request.', array('status' => 401));
        }

        return true;
    }
}

new Custom_WP_Post_API();
