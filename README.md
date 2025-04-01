# Mundo Wap CakePHP Test Application

## Requirements
It is highly recommended to use Linux because the project has several bash scripts for easy installation and usage. However, you can also refer to `.sh` files in the `shell` folder to copy and paste `docker compose` commands manually.

This project uses `docker compose`, so if you haven't installed it yet, you can do so by following the official documentation [here](https://docs.docker.com/desktop/install/linux-install/).

Make sure the ports 13001 and 3306, defined in `docker-compose.yml`, are available.

## Installation
To make this easier, all the commands necessary to install, run and access the application can be done through the `exec.sh` file in the project root.

Follow the steps bellow:

1. Give execution permissions to executable files:
   ```bash
   sudo chmod -R +x exec.sh shell app/bin
   ```
2. Create the `.env.app` and the `.env.db` files according to the related `.env.*.example` files executing the below command:
   ```bash
   ./exec.sh build-env
   ```
   or you can simply copy and rename the example files.
3. Build the docker image and install the application dependencies with the below command:
   ```bash
   ./exec.sh install
   ```

## Usage
1. Execute the below command to start the application:
   ```bash
   ./exec.sh start
   ```
   After installed and started, the application should be accessible at [13001](http://localhost:13001) port and the database should be accessible at [3306](http://localhost:3306).
2. Execute the below command to stop the application:
   ```bash
   ./exec.sh stop
   ```
3. You may need to enter the application command line to execute migrations or install composer packages, you can do this by executing the below command:
   ```bash
   ./exec.sh bash
   ```
   This will open a `bash` command line inside docker `app` container, then you can execute commands like:
   ```bash
   bin/cake migrations migrate
   ```
   ```bash
   composer update
   ```
   To exit the `bash` command line, type:
   ```bash
   exit
   ```
4. If you prefer, you can also use the `./exec.sh` script to execute commands directly into `app` container. The following examples would have the same effect as those in the previous step:
   ```bash
   ./exec.sh bin/cake migrations migrate
   ```
   ```bash
   ./exec.sh composer update
   ```

## Important instructions
Skills with containers and environment management are not the focus of this test, so in case of any issues creating, starting or executing the environment, please contact us.

The database structure should be created according to the `db_structure.sql` file.

Click [here](https://bit.ly/MWDevTestPHP) to see the test specifications, requirements and instructions.

### Authentication
If your implementation does not use CSRF authentication, you should remove the `Cake\Http\Middleware\CsrfProtectionMiddleware` at the `App\Application::middleware` method.

### Configure XDebug (optional)
Set the `XDEBUG_SESSION` key at the request cookies.

At your IDE, point the `app` project directory to the `/var/www/html` absolute path on server.
