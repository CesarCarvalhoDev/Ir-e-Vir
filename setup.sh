#!/bin/bash
# 🚀 Script de Início Rápido - Ir e Vir

echo "================================================"
echo "🅿️  Ir e Vir - Sistema de Autenticação"
echo "================================================"
echo ""

# Verificar se está na pasta raiz do projeto
if [ ! -d "back-end-ir-e-vir" ] || [ ! -d "front-end-ir-e-vir" ]; then
    echo "❌ Erro: Este script deve ser executado na pasta raiz do projeto"
    echo "   Estrutura esperada: front-end-ir-e-vir/ e back-end-ir-e-vir/"
    exit 1
fi

echo "📦 Configurando Backend..."
echo ""

# Backend
cd back-end-ir-e-vir

# Verificar se composer.json existe
if [ ! -f "composer.json" ]; then
    echo "❌ Erro: composer.json não encontrado"
    exit 1
fi

# Instalar dependências
echo "📥 Instalando dependências do Laravel..."
composer install

# Configurar .env
if [ ! -f ".env" ]; then
    echo "⚙️  Copiando .env.example para .env..."
    cp .env.example .env
fi

# Gerar app key
echo "🔑 Gerando APP_KEY..."
php artisan key:generate

# Executar migrações
echo "🗄️  Executando migrações..."
php artisan migrate --force

# Executar seeders
echo "🌱 Carregando dados de teste..."
php artisan db:seed

# Voltar para raiz
cd ..

echo ""
echo "📦 Configurando Frontend..."
echo ""

# Frontend
cd front-end-ir-e-vir

# Verificar se package.json existe
if [ ! -f "package.json" ]; then
    echo "❌ Erro: package.json não encontrado"
    exit 1
fi

# Instalar dependências
echo "📥 Instalando dependências do Vue..."
npm install

# Voltar para raiz
cd ..

echo ""
echo "================================================"
echo "✅ Configuração Concluída!"
echo "================================================"
echo ""
echo "🚀 Para iniciar o projeto, execute em dois terminais:"
echo ""
echo "Terminal 1 (Backend):"
echo "  cd back-end-ir-e-vir"
echo "  php artisan serve"
echo ""
echo "Terminal 2 (Frontend):"
echo "  cd front-end-ir-e-vir"
echo "  npm run dev"
echo ""
echo "📍 URLs de Acesso:"
echo "  Backend:  http://localhost:8000"
echo "  Frontend: http://localhost:5173"
echo ""
echo "👤 Credenciais de Teste:"
echo "  Admin:   admin@irever.com / password123"
echo "  User 1:  usuario1@example.com / password123"
echo "  User 2:  usuario2@example.com / password123"
echo "  User 3:  usuario3@example.com / password123"
echo ""
echo "📚 Documentação:"
echo "  - AUTENTICACAO_README.md: Guia completo"
echo "  - GUIA_TESTES.md: Testes e verificações"
echo "  - IMPLEMENTACAO_RESUMO.md: Resumo técnico"
echo ""
