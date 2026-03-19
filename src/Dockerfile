FROM alpine:3.21

# Installation d'Apache, PHP 8.4 et des extensions nécessaires
RUN apk add --no-cache \
    apache2 \
    php84 \
    php84-apache2 \
    php84-mysqli \
    php84-pdo \
    php84-pdo_mysql \
    php84-session \
    php84-ctype \
    php84-json

# Configuration d'Apache pour PHP 8.4
# Alpine installe php84, on s'assure qu'Apache charge le module
RUN sed -i 's#AllowOverride None#AllowOverride All#' /etc/apache2/httpd.conf && \
    sed -i 's#^# #g' /etc/apache2/httpd.conf && \
    sed -i 's#\#LoadModule headers_module modules/mod_headers.so#LoadModule headers_module modules/mod_headers.so#' /etc/apache2/httpd.conf && \
    echo "ServerTokens Prod" >> /etc/apache2/httpd.conf && \
    echo "ServerSignature Off" >> /etc/apache2/httpd.conf && \
    mkdir -p /run/apache2

# Redirection des logs Apache vers stdout/stderr pour Docker
RUN ln -sf /dev/stdout /var/log/apache2/access.log && \
    ln -sf /dev/stderr /var/log/apache2/error.log

WORKDIR /var/www/localhost/htdocs

# Copie des fichiers de l'application
COPY . .

# On s'assure que le dossier appartient à l'utilisateur apache (apache sur Alpine)
RUN chown -R apache:apache /var/www/localhost/htdocs

EXPOSE 80

# Lancement d'Apache au premier plan
CMD ["/usr/sbin/httpd", "-D", "FOREGROUND"]
