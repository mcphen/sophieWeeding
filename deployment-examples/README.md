# Deployment Examples for Shared Hosting

This directory contains example files to help you deploy the Sophie Wedding Dream application on shared hosting environments.

## Files Included

1. **deployment-guide.md**
   - Comprehensive guide with step-by-step instructions for deploying to shared hosting
   - Covers both standard deployment and alternative approaches for different hosting configurations

2. **modified-index-example.php**
   - Example of how to modify the `index.php` file when Laravel core files are stored outside the public directory
   - Use this when your hosting provider doesn't allow setting a custom document root

3. **root-htaccess-example.txt**
   - Example `.htaccess` file for the root directory
   - Protects sensitive files and redirects requests to the public directory
   - Rename to `.htaccess` when deploying

4. **php-ini-example.txt**
   - Example PHP configuration settings optimized for Laravel on shared hosting
   - Rename to `php.ini` or `.user.ini` depending on your hosting provider's requirements

## How to Use These Files

1. Read the `deployment-guide.md` first to understand the deployment process
2. Choose the appropriate deployment option based on your hosting environment
3. Use the example files as templates, modifying them as needed for your specific setup
4. Remember to update paths, database credentials, and other configuration values to match your environment

## Important Notes

- Always make a backup of your application before deploying
- Test your application thoroughly after deployment
- Some shared hosting providers may have specific requirements or limitations not covered in these examples
- Contact your hosting provider's support if you encounter issues specific to their environment

## Security Considerations

- Never commit sensitive information (like database credentials) to version control
- Ensure proper file permissions are set to protect sensitive files
- Use HTTPS for production environments
- Keep your Laravel application and its dependencies up to date
