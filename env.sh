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

echo "DB_HOST=${DB_HOST}" >> /app/.env
echo "DB_USER=${DB_USER}" >> /app/.env
echo "DB_PASS=${DB_PASS}" >> /app/.env
echo "BASE_URL=${BASE_URL}" >> /app/.env
echo "TITLE=${TITLE}" >> /app/.env

echo "SMTP_USER=${SMTP_USER}" >> /app/.env
echo "SMTP_PASS=${SMTP_PASS}" >> /app/.env
echo "SMTP_HOST=${SMTP_HOST}" >> /app/.env
echo "SMTP_FROM=${SMTP_FROM}" >> /app/.env
echo "SMTP_NAME=${SMTP_NAME}" >> /app/.env

if [ -n "${DEBUG}" ]; then
    echo "DEBUG=${DEBUG}" >> /app/.env
fi

if [ -n "${ENABLE_SUBSCRIPTIONS}" ]; then
    echo "ENABLE_SUBSCRIPTIONS=${ENABLE_SUBSCRIPTIONS}" >> /app/.env
fi

if [ -n "${DISABLE_USER_METADATA_UPDATE}" ]; then
    echo "DISABLE_USER_METADATA_UPDATE=${DISABLE_USER_METADATA_UPDATE}" >> /app/.env
fi

echo "Variáveis de ambiente salvas com sucesso."
