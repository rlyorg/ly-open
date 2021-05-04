#
# PHP Dependencies
# https://laravel-news.com/multi-stage-docker-builds-for-laravel
#
FROM composer:1 as vendor

COPY database/ database/

COPY composer.json composer.json
COPY composer.lock composer.lock
# COPY auth.json auth.json

RUN composer install \
    --no-dev \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#
# Frontend
## node v14.16.0
FROM node:lts as frontend
COPY . /app
WORKDIR /app

# RUN npm config set registry http://registry.npm.taobao.org
RUN npm install && npm run production

#
# Application
#
FROM drupal:8.9-apache
# https://hub.docker.com/_/drupal

# install the PHP extensions  pcntl
RUN set -ex; \
  apt-get update; \
  apt-get install -y --no-install-recommends \
    vim \
    libonig-dev\
    ffmpeg \
  ; \
  docker-php-ext-install -j "$(nproc)" \
    mbstring \
    pcntl \
    bcmath \
  ; \
  \
  rm -rf /var/lib/apt/lists/* \
  && rm -rf /var/www/html \
  && mkdir /var/www/html

COPY . /var/www/html
COPY --from=vendor /app/vendor/ /var/www/html/vendor/
COPY --from=frontend /app/public/ /var/www/html/public/

COPY docker/start.sh /usr/local/bin/start
WORKDIR /var/www/html

RUN chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R ug+rwx storage bootstrap/cache \
  && chmod u+x /usr/local/bin/start

ENV APACHE_DOCUMENT_ROOT /var/www/html/public/
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

CMD ["/usr/local/bin/start"]
