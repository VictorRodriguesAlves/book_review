# API de Review de Livros

Esta é a documentação da nossa API de reviews de livros. O objetivo é ter uma plataforma simples onde usuários podem avaliar livros e a administração pode moderar o conteúdo através de um sistema de denúncias.

**URL Base:** `/api/`

---

## Autenticação

Para a maioria das operações, como postar uma review ou denunciar outra, o usuário precisa estar autenticado. O fluxo é o padrão:

1.  **Registre-se** no endpoint `/auth/register`.
2.  **Faça login** em `/auth/login` para receber um token de acesso.
3.  **Envie o token** no cabeçalho `Authorization` de suas requisições futuras, no formato `Bearer {seu_token}`.

---

## Endpoints

Abaixo estão todos os endpoints disponíveis, agrupados por funcionalidade.

### Autenticação

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `POST` | `/auth/register` | Cria uma nova conta de usuário. |
| `POST` | `/auth/login` | Autentica um usuário e retorna um token de acesso. |
| `POST` | `/auth/logout` | Invalida o token de acesso do usuário logado. |

### Livros e Reviews

| Método | Endpoint | Descrição                                 |
| :--- | :--- |:------------------------------------------|
| `GET` | `/books` | Retorna uma lista de todos os livros.     |
| `POST` | `/books` | Adiciona um novo livro ao catálogo. **(Apenas Admins)**       |
| `GET` | `/books/{book_id}` | Busca os detalhes de um livro específico. |
| `GET` | `/books/{book_id}/reviews`| Lista todas as reviews de um livro.       |
| `POST` | `/books/{book_id}/reviews`| Publica uma nova review para um livro.    |

### Moderação (Denúncias)

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `POST` | `/reviews/{review_id}/reports`| Cria uma denúncia para uma review específica. |
| `GET` | `/reports` | Lista todas as denúncias pendentes. **(Apenas Admins)** |

---

## Estrutura do Banco de Dados

Abaixo está a descrição das tabelas que compõem o banco de dados da aplicação.

### Tabela `users`
Armazena os dados de login, informações pessoais e o nível de acesso de cada usuário.

| Coluna | Tipo | Descrição                                           |
| :--- | :--- |:----------------------------------------------------|
| `id` | BIGINT (PK) | Identificador único do usuário.                     |
| `name` | VARCHAR | Nome do usuário.                                    |
| `email` | VARCHAR | Endereço de e-mail único do usuário.                |
| `password` | VARCHAR | Senha criptografada do usuário.                     |
| `user_type`| ENUM | Nível de permissão do usuário: `['admin', 'user']`. |
| `created_at`| TIMESTAMP | Data e hora de criação do registro.                 |
| `updated_at`| TIMESTAMP | Data e hora da última atualização do registro.      |

### Tabela `books`
Guarda as informações sobre os livros disponíveis para review.

| Coluna | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | Identificador único do livro. |
| `title` | VARCHAR | Título do livro. |
| `description`| TEXT | Descrição ou sinopse do livro (opcional). |
| `average_stars` | DECIMAL | Média de estrelas calculada a partir das reviews. |
| `reviews_count` | INT | Contagem total de reviews recebidas. |
| `created_at`| TIMESTAMP | Data e hora de criação do registro. |
| `updated_at`| TIMESTAMP | Data e hora da última atualização do registro. |

### Tabela `reviews`
Onde ficam armazenadas as avaliações que os usuários fazem dos livros.

| Coluna | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | Identificador único da review. |
| `user_id` | BIGINT (FK) | Chave estrangeira que referencia `users.id`. |
| `book_id` | BIGINT (FK) | Chave estrangeira que referencia `books.id`. |
| `body` | TEXT | O texto da avaliação escrita pelo usuário. |
| `stars` | TINYINT | A nota (de 1 a 5 estrelas) dada ao livro. |
| `created_at`| TIMESTAMP | Data e hora de criação do registro. |
| `updated_at`| TIMESTAMP | Data e hora da última atualização do registro. |

### Tabela `reports`
Registra as denúncias de reviews feitas pelos usuários para análise da moderação.

| Coluna | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | Identificador único da denúncia. |
| `review_id` | BIGINT (FK) | Chave estrangeira que referencia a `reviews.id` denunciada. |
| `reporter_id`| BIGINT (FK) | Chave estrangeira que referencia o `users.id` do denunciante. |
| `reason` | TEXT | Justificativa do usuário para a denúncia. |
| `status` | ENUM | Status da denúncia: `['pending', 'resolved', 'dismissed']`. |
| `created_at`| TIMESTAMP | Data e hora de criação do registro. |
| `updated_at`| TIMESTAMP | Data e hora da última atualização do registro. |

### Tabela `personal_access_tokens`
Armazena os tokens de acesso utilizados para autenticação na API.

| Coluna | Tipo | Descrição |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | Identificador único do token. |
| `tokenable_type`| VARCHAR | O tipo de modelo ao qual o token pertence (ex: `App\Models\User`). |
| `tokenable_id`| BIGINT | A chave primária do modelo ao qual o token pertence. |
| `name` | TEXT | O nome dado ao token para identificação. |
| `token` | VARCHAR(64) | O hash do token de acesso (único). |
| `abilities` | TEXT | As permissões/habilidades que o token possui (pode ser nulo). |
| `last_used_at`| TIMESTAMP | Data e hora do último uso do token (pode ser nulo). |
| `expires_at`| TIMESTAMP | Data e hora de expiração do token (pode ser nulo). |
| `created_at`| TIMESTAMP | Data e hora de criação do registro. |
| `updated_at`| TIMESTAMP | Data e hora da última atualização do registro. |