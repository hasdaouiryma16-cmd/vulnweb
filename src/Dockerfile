FROM php:8.4-apache-bookworm

# Mise à jour des packages système pour corriger les vulnérabilités de l'image de base
RUN apt-get update && apt-get upgrade -y && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Configuration Apache sécurisée (optionnel mais recommandé)
# RUN sed -i 's/ServerTokens OS/ServerTokens Prod/' /etc/apache2/conf-available/security.conf && \
#     sed -i 's/ServerSignature On/ServerSignature Off/' /etc/apache2/conf-available/security.conf

COPY . /var/www/html/

# On s'assure que le dossier appartient à l'utilisateur apache
RUN chown -R www-data:www-data /var/www/html
