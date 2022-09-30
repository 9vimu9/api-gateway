FROM docker.io/bitnami/laravel:9
RUN rm -rf /app
RUN rm -rf /opt/bitnami/scripts/laravel/setup.sh
RUN mkdir "app"
COPY ./ /app

COPY ./Dockerfiles/php/setup.sh /opt/bitnami/scripts/laravel
RUN chmod 755 /opt/bitnami/scripts/laravel/setup.sh

COPY ./startup_commands.sh /opt/bitnami/scripts/laravel
RUN chmod 755 /opt/bitnami/scripts/laravel/startup_commands.sh

