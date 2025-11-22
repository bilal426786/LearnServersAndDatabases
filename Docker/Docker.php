<?php

#Docker Pull Image from Docker Hub

//Pull and run hello-world

docker pull hello-world 

#Docker Run Container

//this command will pull the image from the docker hub

docker run hello-world

//this command will run hello-world container from its image if we already pull this image then it will directly run
//else it will pull it from docker hub and then run a container

# Docker run --name flag

docker run --name CONTAINER_NAME or IMAGE_NAME

// by default it give it a default name which is random if we want to work with containers namespace
// we can name it using --name flag

#Docker image, Docker ps and Doceker ps -a Command

Docker image

// this command is use to check which images are installed / pulled / created by dockerizing

Docker ps

//this will show the running containers inside docker

Docker ps -a

//this will display all the running and not running container inside the docker

# Docker Run in Intractive Mode with the Flag -it

docker run -it mysql

// -it is basically two flags -i and -t 
// -i	Keep STDIN open so you can type commands
// -t	Allocate a pseudo-terminal (like a shell)

// by -it we will inside the container it is an environment like shell to run other commands of the container

# Docker start and stop Commands to start and stop container

docker start CONTAINER_NAME or CONTAINER_ID
docker stop CONTAINER_NAME or CONTAINER_ID

// Docker run will create a new container each time and to start existing container
//  we use docker start and for stop we use docker stop


# Docker rmi and Docker rm command for remove

docker rmi IMAGE_NAME
docker rm CONTAINER_NAME

# Tags in Docker for versions

docker pull mysql:8.0 

// tags are written as :8.0 for image version

# Detach mode running of containr -d flag

docker run -d IMAGE_NAME

// by default containers are running in attach mode we can run it in detach mode
// the main difference between attach, interactive and detach mode is 
// attach - it will show us the log and what it prints not let us use the command inside the container
// intractive - it will let us write commands, it provides us a shell like environment of the container inside
// detach - it will not provide us logs and not the shell env for commands
// manual attach - docker attach CONTAINER_NAME is the method to attach a running container which is detached
// but the manual attach will not let us use the shell environment of the container it just attach for logs

# Manual Attach the container which is detach

docker attach CONTAINER_NAME

// manual attach - docker attach CONTAINER_NAME is the method to attach a running container which is detached
// but the manual attach will not let us use the shell environment of the container it just attach for logs

# Attach in intractive mode a detach container

docker exec -it CONTAINER_NAME sh
docker exec -it CONTAINER_NAME /bin/sh
docker exec -it CONTAINER_NAME bash
docker exec -it CONTAINER_NAME /bin/bash

// this is the method for attaching the detach container in intractive mode
// and simply type exit to get out of the shell not stop the container

# Environment Variables in Docker for MySql -e flag for env

docker run -d -e MYSQL_ROOT_PASSWORD=root --name mysql-80 mysql:8.0

// this way we can store environment variables for mysql using -e flag and --name flag for custom name

# Port Binding in docker -p flag

// docker container have its own port to make them available to host machine we use port binding 

docker run -p8080:3306 IMAGE_NAME 

// -p HOST_PORT : CONTAINER_PORT
// -p is the flag 8080 is the port of the host maching which is binded to 3306 port of the container
// FOR EXAMPLE:
docker run -d -e MYSQL_ROOT_PASSWORD=root --name mysql-80 -P8080:3306 mysql:8.0

# Docker Troubleshoot Commands  docker logs, docker exec

docker logs CONTAINER_ID or CONTAINER_NAME

# Docker Network to let container communicate with each other over the network

docker network create NETWORK_NAME 
i.e
docker network create mongo-network

// and to delete any network we use 

docker network rm NETWORK_NAME

// Now to run a container inside the network we use --network flag

docker run -d -p27017:27017 --name mongo -e ROOT_USERNAME=root -e ROOT_PASSWORD=admin --network mongo-network mongodb


// we can write commands line by line using \ backslash

docker run -d \
-p27017:27017 \
--name mongo \
-e ROOT_USERNAME=root \
-e ROOT_PASSWORD=admin \
--network mongo-network \
mongo

// one new environment variable for mongo-express gui is its url 
// ME_CONFIG_MONGODB_URL="mongodb://USERNAME:PASSWORD@MONGO_CONTAINER_NAME:MONGO_DB_PORT"
// ME_CONFIG_MONGODB_URL="mongodb://root:root@mongodb:27017"

// when we want to hit mongo-express then we need to hit localhost:8081 because we write mongo-express to bind over 8081
// and under the hood using env var set to connect mongo-express to mongodb over port 27017
// so mongo express ask for id and password which is by default admin and pass

#Docker Compose (with using a markup language .yaml and this is indentation senstive)


//inside mongodb.yaml

version: "3.8"

services:
    mongo:
        image: mongo
        ports:
        - 27017:27017
        environment:
        - MONDO_INITDB_ROOT_USERNAME=admin
        // Or
            MONDO_INITDB_ROOT_USERNAME: admin
            // we can use both way but : method is better and tab is necessary in place of -
    mongo-express:
        image: mongo-express
        ports:
        - 8081:8081
        environment:
            ME_CONFIG_MONGODB_ADMINIUSERNAME: admin
            ME_CONFIG_MONGODB_ADMINPASSWORD: root
            ME_CONFIG_MONGODB_URL: "mongodb://admin:root@mongo:27014" 
            // Or
            ME_CONFIG_MONGODB_URL: mongodb://admin:root@mongo:27014/
            // we can remove " double quotes around the url and place / forward slash at the end of url

// We didn't define any network inside yaml file because it by default run all services written in yaml file
// inside a default network but we can write networks inside multiple services


//inside docker-compose.yaml

version: "3.8"

services:
    mongo:
        image: mongo
        ports:
        - 27017:27017
        network:
        - db_network
        environment:
        - MONDO_INITDB_ROOT_USERNAME=admin
        // Or
            MONDO_INITDB_ROOT_USERNAME: admin
            // we can use both way but : method is better and tab is necessary in place of -
    mongo-express:
        image: mongo-express
        ports:
        - 8081:8081
        network:
        - db_network
        environment:
            ME_CONFIG_MONGODB_ADMINIUSERNAME: admin
            ME_CONFIG_MONGODB_ADMINPASSWORD: root
            ME_CONFIG_MONGODB_URL: "mongodb://admin:root@mongo:27014" 
            // Or
            ME_CONFIG_MONGODB_URL: mongodb://admin:root@mongo:27014/
            // we can remove " double quotes around the url and place / forward slash at the end of url
    node:
        image: node
        ports:
        -5000:5000
        network:
        - app-network

networks:
    db_network:
        driver: bridge
    app_network:
        driver: bridge

// to create containers from this yaml file we use docker commands

docker compose -f FILE_NAME.yaml up -d
docker compose -f FILE_NAME.yaml down



# Dockerizing our Apps (In production jinkins uses for this work)

// We create a Dockerfile inside app, name starts with Capital D letter and have no Extention i.e Dockerfile

//We need some keys

#FROM
// From is the base image which is use to run out image every image have its base image layer
FROM node

#ENV
// ENV is use to write environment variables 
ENV MONGO_DB_USERNAME=admin \
    MONGO_DB_PWD=root

#RUN
// this is use to run a command 
RUN mkdir -p testapp

#COPY
// copy is use to copy the root directory inside the folder make using run command which is testapp
COPY . /testapp 
// mean . which is in root directory (. will tell that the root directory)
// and copy all that to /testapp which is just created in the same root directory

#CMD
// this is an array of commands which need to executes
CMD ["node", "/testapp/server.js"]
// we earlier write node server.js in terminal to run but now we copy the files inside testapp so we write the complete address

#WORKEDIR
// workdir is use like cd in terminal it tells below the written commands will work as per this directory
// this is really not necessary but it for better understanding
WORKDIR /testapp
// now below this workdir any run command will run inside this /testapp like cd in terminal

#EXPOSE
// this is basically use to write port which port is use for this image when run on container but
// this is not binded to host maching it is just to inform docker and 
// to bind this port to host machine port we should run 
// docker run -p3000:3000 IMAGE_NAME
EXPOSE 3000

#To build this Dockerizing Image we use command

docker build -t testapp:1.0 .

// in this command we write the docker build command and at the end we write . which is root directory
// where we are building image so we write that address of root as . dot

// and to check the image is successfully created we can hit docker image 

// by this example of dockerizing we dockerized our test app with node modules 
// if we skip that node modules we should another RUN npm install to install the node modules inside our app

#To remove unnecessary files from binding we use .dockerignore file

// inside .dockerignore
node_modules
npm-debug.log
.env
Dockerfile
docker-compose.yml
.git

// we should ignore all above files to build inside our image we didn't need them just ignore it
// we can delete node_module during build time but not necessary we can ignore it using .dockerignore


#Docker Volume

// we use -v flag to attache a volume to our container and when we delete or restar the container it will not be deleted it will persist

docker run -it -v /user/bilalpersonal/desktop/data:/test/data ubuntu

// we should provide absolute path of our host machine mean complete path
// and the relative path of our container like /test/data 
// this will create a volume and attach to the container we are running

// And to do this through yaml file we write

volume:
    - HOST_ABSOLUTE_DIR:CONTAINER_DIR
    // For mongo db (documentation)
    - /users/bilalpersonal/desktop/data:/mongo/db


// to check custom volume we created

docker volume ls

// to Create a custom Volume

docker volume create VOLUME_NAME

// during creating of named volume we didn't specified the path of the volume where we want to create on host maching
// so by default docker will create this volume on default paths different for mac and win
// for Mac or Linux /var/lib/docker/volumes
// and for windows C:\ProgramData\Docker\volumes

// and to Remove a volume

docker volume rm VOLUME_NAME

// To Connect the Custom Named Volume to any container we have three methods
// Named Volume
docker run -v VOLUME_NAME:CONTAINER_DIR
// if the VOLUME_NAME volume is created already in docker it will attach if not found then it will create and attach
// Anonymous Volume
docker run -v MOUNT_PATH
// in this we just pass the path of container dir and a random vol is create and assigned we use this for temp vol and later del them with prune command
// Bind Mount
docker run -v HOST_DIR:CONT_DIR
// in bind care host maching is managing the volume but in mount or named volume docker do this

//Docker Volumes Prune

docker volume prune 
// this will delete anonymus volume those are unused

#Docker Network Drivers

// Bydefault bridge dirver is connected to the networks

