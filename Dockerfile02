# Utiliser une image Nginx comme base
FROM nginx

&&
# Copier les fichiers de configuration Nginx
COPY ./default.conf /etc/nginx/conf.d/default.conf

&&
# Installer PHP et les dépendances nécessaires
RUN apt-get update \
    && apt-get install -y \
    php-fpm \
    php-mbstring \
    php-xml \
    php-zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

&&
# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

&&
# Créer le répertoire racine de l'application
RUN mkdir -p /var/www/html

&&
WORKDIR /var/www/html

&&
# Copier les fichiers de l'application dans le conteneur
COPY . /var/www/html

&&
# Installer les dépendances de l'application 
RUN composer install --prefer-dist --no-interaction --no-progress --ignore-platform-req=ext-curl

&&
# RUN mkdir migrations
# RUN chmod 777 migrations.sh
# RUN  ./migrations.sh
# Fixer les permissions des fichiers Symfony
# RUN chown -R www-data:www-data var

# Port d'écoute de Nginx
EXPOSE 80
&&
# Commande pour exécuter Nginx
CMD ["nginx", "-g", "daemon off;"]
