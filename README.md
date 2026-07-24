Social Media Platform API

A modern RESTful Social Media API built with Laravel, featuring authentication, user interactions, content management, notifications, and personalized feeds.

Overview

This project is a backend API for a social media platform where users can create posts, interact with content, follow other users, receive notifications, and discover trending content.

The application is built using Laravel and follows RESTful API principles with secure authentication powered by Laravel Sanctum.

---

Features

Authentication

- User Registration
- User Login
- User Logout
- Token-Based Authentication (Laravel Sanctum)
- Protected API Routes

User Management

- User Profiles
- Update Profile Information
- View User Posts
- Suggested Users

Social Features

- Follow Users
- Unfollow Users
- Followers & Following Lists
- User Feed

Posts

- Create Post
- Update Post
- Delete Post
- View Posts
- View Single Post
- Most Viewed Posts
- Most Liked Posts

Engagement

- Like / Unlike Posts
- Bookmark (Save) Posts
- Repost Posts
- Post View Tracking

Comments & Replies

- Create Comments
- Update Comments
- Delete Comments
- Reply to Comments
- Manage Replies

Hashtags

- Automatic Hashtag Extraction
- Hashtag Association with Posts
- Search Posts by Hashtag
- Trending Hashtag Ready Structure

Notifications

- Real-Time Ready Notification System
- Like Notifications
- Follow Notifications
- Custom Notification Events

Authorization

- Laravel Policies
- Resource Ownership Protection
- Secure Access Control

---

Tech Stack

- PHP 8+
- Laravel 12
- Laravel Sanctum
- MySQL
- Eloquent ORM
- REST API Architecture

---

Database Relationships

User

- Has Many Posts
- Has Many Comments
- Has Many Replies
- Belongs To Many Followers
- Belongs To Many Following
- Belongs To Many Liked Posts
- Belongs To Many Saved Posts

Post

- Belongs To User
- Has Many Comments
- Has Many Views
- Belongs To Many Likes
- Belongs To Many Hashtags
- Belongs To Many Saves

Comment

- Belongs To User
- Belongs To Post
- Has Many Replies

Reply

- Belongs To User
- Belongs To Comment

Notification

- Belongs To User

---

API Architecture

The project follows a layered Laravel architecture using:

- Controllers
- Models
- Policies
- Notifications
- Events
- Listeners
- Form Requests
- API Resources

---

Installation

Clone Repository

git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git

Navigate To Project

cd YOUR_REPOSITORY

Install Dependencies

composer install

Environment Setup

cp .env.example .env

Generate Application Key

php artisan key:generate

Configure Database

Update your ".env" file:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=social_media
DB_USERNAME=root
DB_PASSWORD=

Run Migrations

php artisan migrate

Start Development Server

php artisan serve

---

Authentication

This API uses Laravel Sanctum.

After login, include the token in every protected request:

Authorization: Bearer YOUR_TOKEN

---

Example Endpoints

Authentication

POST /api/register
POST /api/login
POST /api/logout

Users

GET /api/users
GET /api/users/{id}

Posts

GET /api/posts
POST /api/posts
PUT /api/posts/{id}
DELETE /api/posts/{id}

Comments

POST /api/posts/{post}/comments

Likes

POST /api/posts/{post}/like

Follow System

POST /api/users/{user}/follow

Notifications

GET /api/notifications

---

Security

- Laravel Sanctum Authentication
- Route Protection Middleware
- Authorization Policies
- Request Validation
- Mass Assignment Protection

---

Future Improvements

- Image Uploads
- Stories
- Direct Messaging
- Real-Time Notifications
- Explore Page
- Trending System
- User Blocking
- Reporting System

---

Learning Goals

This project was built to practice and improve knowledge in:

- Laravel Framework
- RESTful API Development
- Eloquent Relationships
- Query Builder
- Authentication & Authorization
- Events & Listeners
- Notifications
- API Design Principles

---

License

This project is open-source and available under the MIT License.
