@echo off
REM 🚀 Script de Início Rápido - Ir e Vir (Windows)

echo.
echo ================================================
echo 🅿️  Ir e Vir - Sistema de Autenticação
echo ================================================
echo.

REM Verificar se está na pasta raiz do projeto
if not exist "back-end-ir-e-vir" (
    echo ❌ Erro: Pasta back-end-ir-e-vir não encontrada
    pause
    exit /b 1
)

if not exist "front-end-ir-e-vir" (
    echo ❌ Erro: Pasta front-end-ir-e-vir não encontrada
    pause
    exit /b 1
)

echo 📦 Configurando Backend...
echo.

cd back-end-ir-e-vir

REM Verificar se composer.json existe
if not exist "composer.json" (
    echo ❌ Erro: composer.json não encontrado
    pause
    exit /b 1
)

REM Instalar dependências
echo 📥 Instalando dependências do Laravel...
call composer install

REM Configurar .env
if not exist ".env" (
    echo ⚙️  Copiando .env.example para .env...
    copy .env.example .env
)

REM Gerar app key
echo 🔑 Gerando APP_KEY...
php artisan key:generate

REM Executar migrações
echo 🗄️  Executando migrações...
php artisan migrate --force

REM Executar seeders
echo 🌱 Carregando dados de teste...
php artisan db:seed

cd ..

echo.
echo 📦 Configurando Frontend...
echo.

cd front-end-ir-e-vir

REM Verificar se package.json existe
if not exist "package.json" (
    echo ❌ Erro: package.json não encontrado
    pause
    exit /b 1
)

REM Instalar dependências
echo 📥 Instalando dependências do Vue...
call npm install

cd ..

echo.
echo ================================================
echo ✅ Configuração Concluída!
echo ================================================
echo.
echo 🚀 Para iniciar o projeto, execute em dois terminais:
echo.
echo Terminal 1 (Backend):
echo   cd back-end-ir-e-vir
echo   php artisan serve
echo.
echo Terminal 2 (Frontend):
echo   cd front-end-ir-e-vir
echo   npm run dev
echo.
echo 📍 URLs de Acesso:
echo   Backend:  http://localhost:8000
echo   Frontend: http://localhost:5173
echo.
echo 👤 Credenciais de Teste:
echo   Admin:   admin@irever.com / password123
echo   User 1:  usuario1@example.com / password123
echo   User 2:  usuario2@example.com / password123
echo   User 3:  usuario3@example.com / password123
echo.
echo 📚 Documentação:
echo   - AUTENTICACAO_README.md: Guia completo
echo   - GUIA_TESTES.md: Testes e verificações
echo   - IMPLEMENTACAO_RESUMO.md: Resumo técnico
echo.
pause
