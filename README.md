# HESK - Free Help Desk Software

**HESK** é um software de help desk gratuito, desenvolvido em PHP, simples e flexível, projetado para ser fácil de instalar e gerenciar. Ideal para pequenas e médias equipes, ele ajuda a organizar e responder de forma eficiente às solicitações de suporte dos clientes.

## 🌟 Recursos Principais

-   **Sistema de Tickets:** Fluxo simplificado de tickets, desde a submissão pelo cliente até a resolução final.  
-   **Base de Conhecimento Integrada:** Reduz o volume de tickets ao permitir que os clientes encontrem soluções por conta própria.  
-   **Interface Responsiva:** Otimizada para dispositivos móveis, garantindo boa experiência em qualquer tela.  
-   **Instalação Rápida:** Fácil de configurar e pronto para uso em poucos minutos.

## ⚙️ Como Funciona

O fluxo de trabalho do HESK é simples:

1. O cliente envia um ticket com sua dúvida ou problema.  
2. O ticket é atribuído automaticamente a um membro da equipe de suporte.  
3. O agente analisa e responde ao ticket.  
4. O ticket é encerrado quando o problema é resolvido.

## 📁 Instalação

A instalação do HESK é feita por meio de download e requer apenas um servidor com suporte a PHP. A seguir, instruções mais detalhadas com base nos guias oficiais.

### 🔧 Requisitos Básicos

- Servidor web com suporte a **PHP**.  
- **MySQL** (ou compatível) para armazenar os dados (nome do banco, usuário, senha).  
- Permissão de escrita em pastas onde será instalado (para upload, criação de arquivos de sessão, etc.).  
- Acesso FTP (ou outro método de upload) para enviar os arquivos ao servidor.

### 🚀 Passos de Instalação

1. Faça upload de todos os arquivos do HESK para uma pasta pública do seu servidor (por exemplo: `public_html/helpdesk`). :contentReference[oaicite:0]{index=0}  
2. Acesse a pasta `install` pelo navegador (por exemplo: `https://seu-dominio.com/helpdesk/install`). :contentReference[oaicite:1]{index=1}  
3. Siga os quatro passos do instalador web:  
   - Licença — aceite os termos. :contentReference[oaicite:2]{index=2}  
   - Verificação de requisitos — confira se o servidor atende às configurações necessárias. :contentReference[oaicite:3]{index=3}  
   - Configuração do banco de dados — informe nome, usuário, senha e host do banco MySQL. :contentReference[oaicite:4]{index=4}  
   - Criação das tabelas do banco de dados. :contentReference[oaicite:5]{index=5}  
4. Após a instalação, **delete o diretório `install`** do servidor. :contentReference[oaicite:6]{index=6}  
5. Entre na área administrativa via navegador, por exemplo: `https://seu-dominio.com/helpdesk/admin` :contentReference[oaicite:7]{index=7}  
6. Complete configurações iniciais:  
   - Perfil (nome, email, assinatura, nova senha) :contentReference[oaicite:8]{index=8}  
   - Configurações gerais (URL do site, título, outras preferências) :contentReference[oaicite:9]{index=9}  
   - Departamentos ou categorias de atendimento :contentReference[oaicite:10]{index=10}  
   - Equipe de suporte (criar contas de agente) :contentReference[oaicite:11]{index=11}  
   - Base de conhecimentos (criar categorias e artigos) e respostas prontas (“canned responses”) :contentReference[oaicite:12]{index=12}  

### 🔄 Upgrade de uma Versão Anterior do HESK

Se você já está usando outra versão do HESK e quer atualizar:

1. Faça **backup** completo do banco de dados e dos arquivos existentes. :contentReference[oaicite:13]{index=13}  
2. Substitua os arquivos antigos pelos novos, **exceto**:  
   - `hesk_settings.inc.php`  
   - `head.txt`  
   - `header.txt`  
   - `footer.txt` :contentReference[oaicite:14]{index=14}  
3. Se você renomeou a pasta `/admin`, lembre-se de mover ou ajustar os arquivos novos correspondentes. :contentReference[oaicite:15]{index=15}  
4. Acesse novamente a pasta `install` pelo navegador, clique em “UPDATE HESK” e siga o procedimento de atualização das tabelas do banco de dados. :contentReference[oaicite:16]{index=16}  
5. Depois da atualização, delete o diretório `install`. :contentReference[oaicite:17]{index=17}  
6. Verifique se tudo funciona: login no admin, visualização correta das personalizações (título, campos customizados etc.). :contentReference[oaicite:18]{index=18}  

## 💰 Planos Disponíveis

O HESK oferece duas modalidades:

- **Versão Gratuita:** Ideal para equipes que desejam hospedar o sistema por conta própria.  
- **Planos na Nuvem (Cloud):** Opções pagas para quem prefere uma solução hospedada, com preços fixos e manutenção incluída.

---

## 📚 Referências

- Quick Install Guide — HESK Documentação oficial :contentReference[oaicite:19]{index=19}  
- Step by Step Install Guide — HESK Documentação oficial :contentReference[oaicite:20]{index=20}  
- Conhecimentos adicionais disponíveis no site de suporte e base de conhecimento do HESK.

---

## ⚠️ Observações Finais

- Senhas no HESK são **sensíveis a maiúsculas e minúsculas** (case sensitive). :contentReference[oaicite:21]{index=21}  
- Nome de usuário **não** é sensível a maiúsculas (não diferencia: “Admin” vs “admin”). :contentReference[oaicite:22]{index=22}  
- É recomendado realizar upgrades em horários de baixo tráfego para evitar interrupções. :contentReference[oaicite:23]{index=23}  
- Após qualquer instalação ou atualização, teste todas as funcionalidades básicas para garantir que tudo esteja corretamente configurado.

---

**Nota:** Este README foi elaborado com base nas informações disponíveis no site oficial do HESK. Para recursos avançados ou soluções corporativas, o site recomenda o parceiro [SysAid](https://www.sysaid.com/).
