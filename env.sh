#!/bin/bash

echo "Validando variáveis de ambiente."

check_env_var() {
    var_name=$1
    if [ -z "${!var_name}" ]; then
        echo "Error: A variável de ambiente $var_name não está definida ou está vazia." >&2
        exit 1
    fi
}

REQUIRED_ENV_VARS=(
    "DB_HOST"
    "DB_USER"
    "DB_PASS"
    "BASE_URL"
    "TITLE"
	"SMTP_USER"
    "SMTP_PASS"
    "SMTP_HOST"
    "SMTP_FROM"
    "SMTP_NAME"
)

for var in "${REQUIRED_ENV_VARS[@]}"; do
    check_env_var "$var"
done

echo "Todas as variáveis de ambiente obrigatórias estão definidas."

echo "Criando variáveis arquivo de variaveis de ambiente."

echo "DB_HOST=${DB_HOST}" >> /var/www/html/.env
echo "DB_USER=${DB_USER}" >> /var/www/html/.env
echo "DB_PASS=${DB_PASS}" >> /var/www/html/.env
echo "BASE_URL=${BASE_URL}" >> /var/www/html/.env
echo "TITLE=${TITLE}" >> /var/www/html/.env

echo "SMTP_USER=${SMTP_USER}" >> /var/www/html/.env
echo "SMTP_PASS=${SMTP_PASS}" >> /var/www/html/.env
echo "SMTP_HOST=${SMTP_HOST}" >> /var/www/html/.env
echo "SMTP_FROM=${SMTP_FROM}" >> /var/www/html/.env
echo "SMTP_NAME=${SMTP_NAME}" >> /var/www/html/.env

if [ -z "${DEBUG}" ]; then
    DEBUG=null
fi
echo "DEBUG=${DEBUG}" >> /var/www/html/.env

if [ -z "${ENABLE_SUBSCRIPTIONS}" ]; then
    ENABLE_SUBSCRIPTIONS=false
fi
echo "ENABLE_SUBSCRIPTIONS=${ENABLE_SUBSCRIPTIONS}" >> /var/www/html/.env

if [ -z "${DISABLE_USER_METADATA_UPDATE}" ]; then
    DISABLE_USER_METADATA_UPDATE=false
fi
echo "DISABLE_USER_METADATA_UPDATE=${DISABLE_USER_METADATA_UPDATE}" >> /var/www/html/.env

echo "Variáveis de ambiente salvas com sucesso."
