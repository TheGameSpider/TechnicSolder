# Installation
You will need to setup your server with a proper software stack.
Ubuntu 18.04: https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-ubuntu-18-04

```bash
$ apt install git
$ cd /var/www
$ git clone https://github.com/TheGameSpider/TechnicSolder.git
```

The you should change the nginx configuration

nginx: Change **root /var/www/html** to **root /var/www/TechnicSolder**

You can also add **& ~E_NOTICE** to *error_reporting* in php.ini (if it isn't alreathe there)

# TechnicSolder Configuration
TODO
