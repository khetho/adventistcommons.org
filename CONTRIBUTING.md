
Adventistcommons is developed by developers all around the world. Here are technical stuff about how to participate, if you will.

## Docker Development Setup Guide

Docker is a solution to make services work in isolated containers. No need to have local lamp, apache, mysql, php, composer … 

Just install docker and docker-compose and follow steps :
- clone the repository somewhere on your computer
- Set right on var dirs ``chmod 777 ./var -R`` (may not needed)
- Point your terminal project root and launch project with ``sudo docker-compose up``
- run migration with command ```docker-compose exec ac-php-fpm bin/console do:mi:mi```
- run fixtures (base sample data) with command ```docker-compose exec ac-php-fpm bin/console do:fi:lo```
- install frontend deps with command ```docker-compose exec ac-node yarn install```
- build frontend with command ```docker-compose exec ac-node yarn run dev```
- In your browser, go to localhost:8096 (the application) and create your account
- In your browser, go to localhost:8080 (adminer), and connect with parameters Mysql / ac-db / root / somePassword
- Create ssl private and public keys for relation with frontend (see below(#jwt))
- In your browser, go to localhost:8097 (the admin application) and create your account

## Manual Development Setup Guide

Follow the steps below to setup AdventistCommons on your local development environment. We assume you already have a functioning localhost environment with webserver (Apache, Nginx …), PHP and MySQL installed. Instructions are for Windows, Mac OS and Linux.

Let us know if you have any issues with these steps.

### Code base

- Clone the repository on your localhost environment.
- Setup your webserver vhost. We recommend setting up adventistcommons.local as a server alias and pointing it to the directory ```/public``` from where you cloned the repository.
- Install composer : Point your terminal client to the application/ directory, and run `php -r "readfile('https://getcomposer.org/installer');" | php -c php.ini`
- Install dependencies with composer with ```php composer.phar install```
- You may need to install code igniter’s twig extension with command ``php vendor /kenjis/codeigniter-ss-twig/install.php``

### Database

- Create database in your favorite MySQL client or with command line : ```mysql -u root -pPASSWORD -e "create database DBNAME;"``` (replace PASSWORD and DBNAME with real data)
- Play migration to have a database up to date : ```php bin/console do:mi:migrate```
- For development purpose, you can run some fixtures which are some basic data : ```php bin/console do:fi:lo``` you can log-in with the account ```admin``` / ```pass```

### Config

- copy file ```.env``` to ```.env.local```
- Edit the file ```.env.local``` to set parameters such as database informations
- beware to never commit that file

### Frontend

- install dependencies : ```sudo docker-compose run ac-node npm install --dev```
- compile all assets for frontend, dev mode : ```sudo docker-compose run ac-node npm run dev```
- compile all assets for frontend, and keep on watching for changes in files : ```sudo docker-compose run ac-node npm run watch```
- compile and publish for production : ```sudo docker-compose run ac-node npm run prod```

### JWT

In order to be able to authentify frontend, application must sign messages with frontend. Generate keys like this :
```
    mkdir -p config/jwt
    jwt_passhrase=$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')
    echo "$jwt_passhrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
    echo "$jwt_passhrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
    setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
    setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
```

## Software elements

### Symfony

First base of the application. If you do not know it yet, check this : https://symfony.com/

If you need to do anything, it’s a good point to start with the controller, in ```src/Controller/``` folder.  

### Migrations

Databases changes are handled by doctrine migrations system.
To play all migrations, run the command ```php bin/console do:mi:migrate```.
The migrations already executed on your system are stored, so you can play many times safely,
only not-applied-yet migrations will be applied. When you get others work from code base
(git pull or merge), you must play migrations other developers may have added,
with same command : ```php bin/console do:mi:migrate```.

The idea of migration is to keep a trace of changes done in database, which can be written
as SQL code. Migrations are stored in ```/src/Migrations```.

If you want to add a change in database follow these steps :
- apply changes to the entity(ies) : create new, add a field, change a type …
- create the migration ```php bin/console do:mi:diff```
- edit to check the new migration file, created in `/src/Migrations/`.
- play your migration with ```php bin/console do:mi:mi```
- do not forget to test the down with ```php bin/console do:mi:mi prev``` 

And from now, never do structural changes directly in database, use migrations !

In addition, you can delete database and recreate it all from beginning, including dev sample data with the command
```sudo docker-compose exec ac-php-fpm bin/clear-db`` 

### Testing

PHP part is tested with phpspecs and phpunit.
* Run phpunit tests with ```sudo docker-compose exec ac-php-fpm bin/phpunit```
* Run phpspec tests with ```sudo docker-compose exec ac-php-fpm vendor/bin/phpspec run```

### Symfony API backend

We use Apiplatform for the backend

### Angular Frontend

Some content soon here


## Deployment

### destination server reqs

Nginx, Mysql and PHP 7.4 are required
```
sudo apt-get install unzip nginx
sudo apt-get install php-fpm php7.4-xml php7.4-mysql php7.4-mbstring php7.4-zip php7.4-curl
```
Nginx vhost config is also required, only if you want to see it in a browser.

### deployment point (your machine !) requirements

Nodejs :
```
curl -sL https://deb.nodesource.com/setup_13.x | sudo -E bash -
sudo apt-get install -y nodejs
```

Composer :
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'c5b9b6d368201a9db6f74e2611495f369991b72d9c8cbd3ffbc63edff210eb73d46ffbfce88669ad33695ef77dc76976') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mv composer.phar /usr/bin/composer     # root rights needed
```

Ansible > 2.8 is needed :
```
sudo pip install ansible
```

### testing and debugging the deployment

Test the deployment (deploy to localhost) :
* Create a virtual machine at name : ac-deploy.local (with docker, virtuabox, or anything else)
* Configure it so you can reach through ssh without password. See ```ssh-copy-id``` command to install your public key on it.
* Install PHP 7.4 on that machine
* Install mysql and create database «adventistcommons» accessible for user «adventistcommons» with teh password «password»
* Run the playbook :
```
cd deploy
ansible-playbook -i deploy/inventories/deploy-tester.ini deploy/tasks/deploy.yml 
```

### deploy

You need to have access to destination through ssh without password.
* For the demo env, you need the pem key to authenticate against destination server. And add it to you ssh agent : ``` ssh-add development_adventist_commons.pem ```. Then test your connection with ssh.
* Deploy to «develop» env (http://develop.adventistcommons.org):
``` 
cd deploy
ansible-playbook -i inventories/develop tasks/deploy.yml --ask-vault-password
```

Note : The vault password is asked to do so. It is available from administrators.

### edit deployment config

Edit the encrypted variables for an environment : ``` ansible-vault edit deploy/inventories/develop ```

## Natural Language Processor

Adventistcommons uses a Python software tool to separate paragraphs into sentences. It is called "nlp-api".
 
Steps to install it :
```shell script
sudo apt-get install python3
sudo apt-get install python3-pip
pip3 install nlp-api
```

To make it run as a service with systemd, create the service :
```shell script
sudo vi  /lib/systemd/system/nlp-api.service
```

And add service code
```
[Unit]
Description=Natural Language Processiong API
After=multi-user.target
Conflicts=getty@tty1.service

[Service]
User=ubuntu
Type=simple
ExecStart=/usr/bin/python3 -m nlp_api localhost 2230
StandardInput=tty-force

[Install]
WantedBy=multi-user.target
```

Starts service
```shell script
sudo systemctl daemon-reload
sudo systemctl enable nlp-api.service
sudo systemctl start nlp-api.service
```