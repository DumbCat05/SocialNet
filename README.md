# SocialNet Project

This is a simple Social Network web application using PHP, MySQL, Nginx, and Linux.

## Pages

- Admin Page: `/admin/newuser.php`
- Sign In Page: `/socialnet/signin.php`
- Home Page: `/socialnet/index.php`
- Setting Page: `/socialnet/setting.php`
- Profile Page: `/socialnet/profile.php`
- About Page: `/socialnet/about.php`
- Sign Out Page: `/socialnet/signout.php`

## Database

Database name: `socialnet`

Table name: `account`

Columns:

- id
- username
- fullname
- password
- description
- created_at

## Setup

Import the database:

```bash
sudo mysql < db.sql
