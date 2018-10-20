=== Boleto Fácil WooCommerce ===
Contributors: luismatias, fcolnaghi, filipebanzoli
Tags: checkout, billet, boleto, boletofacil, cartao, gateway, credito, bankslip, card, credit
Requires at least: 3.5
Tested up to: 4.9.6
Requires PHP: 7.0
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Receba pagamentos com o Boleto Fácil, sem burocracia e sem taxas abusivas.
Novidade! Agora você pode emitir cobranças com cartão de crédito e parceladas com checkout transparente ou pelo **[BoletoBancario.com](https://www.boletobancario.com/boletofacil/user/signup.html)**.

== Description ==
### Como Funciona o BoletoBancario.com ###

[youtube https://www.youtube.com/watch?v=qtZsqJdhwe8]

1. Você emite cobranças pelo nosso sistema para seu cliente final.
2. O cliente faz o pagamento.
3. O valor entra em nossa conta operacional.
4. Transferimos os valores para sua conta.

### Integração ###

A integração é completa, com o sistema de retorno que avisa quando o boleto é pago, e atualiza o status do seu pedido para processando.

### Compatibilidade ###

Compatível com as versões 2.5.x ou maior do WooCommerce.

== Installation ==
A instalação deve seguir os 3 guias abaixo.

### Configuração do Boleto Fácil ###

1. Crie uma conta no [Boleto Fácil](https://www.boletobancario.com/boletofacil/user/signup.html);
2. Com a conta é possível gerar um Token em [Boleto Fácil - Token](https://www.boletobancario.com/boletofacil/integration/integration.html#token);

### Configuração do plugin ###

1. Vá até `WooCommerce > Configurações > Finalizar compra > Boleto Fácil`;
2. Habilite o **Boleto Fácil** e preencha como preferir as opções de *Título* de *Descrição*;
3. Digite o token gerado na sua conta do **Boleto Fácil**;
4. Altere as demais configurações como preferir;
5. Salve as configurações;

### Configuração do plugin para CPF/CNPJ ###

1. Instale e ative o plugin https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/
2. Vá até `WooCommerce > Campos do Checkout`;
3. Escolha \'CPF e CNPJ\' em \'Exibir Tipo de Pessoa\'
4. Marque a opção \'Caso esteja marcado os campos de Pessoa Física e Pessoa Jurídica serão obrigatórios apenas no Brasil.\'
5. Deixe as opções de validação de e-mail, cpf, cnpj todas ativas(Ajuda a evitar fraudes)

== Frequently Asked Questions ==
### O que eu preciso para utilizar este plugin? ###

* Ter instalado o WooCommerce 2.5.x ou superior.
* Ter instalado e configurado o https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/
* Com a conta é possível gerar um Token em [Boleto Fácil - Token](https://www.boletobancario.com/boletofacil/integration/integration.html#token);
* E configure a [Notificação de Pagamentos](https://www.boletobancario.com/boletofacil/integration/integration.html#notificacao);
* Pronto, conta configurada.

### O que é o Boleto Fácil? ###

Nossas soluções atendem a qualquer demanda para emissão de boletos de forma segura, prática e barata.
O BoletoBancario.com conta com diversas alternativas para geração de boletos e recebimento de pagamentos, que beneficiam desde profissionais autônomos até grandes empresas. Com o Boleto Fácil você é livre para emitir quantos boletos quiser, pagando somente quando receber.

###O que eu preciso para utilizar o BoletoBancario.com?###

Para usar o BoletoBancario.com é necessário:

1. Ter uma conta corrente ou poupança em banco (consultar a listagem [aqui](https://www.boletobancario.com/docs/banks.html));
2. Realizar o cadastro no site: www.boletobancario.com, informando linha de negócio e enviando sua documentação. Você receberá um email solicitando cópias dos seguintes documentos:

>*Pessoa Física*

*         RG ou CNH
*         CPF (caso o número não conste no documento de identificação)
*         Comprovante de residência emitido há no máximo 3 meses

>*Pessoa Jurídica*

*         Contrato Social, Requerimento de Empresário ou Certificado de Microempreendedor Individual (MEI)
*         RG ou CNH dos sócios
*         CPF dos sócios (caso o número não conste no documento de identificação)
*         Comprovantes de residência dos sócios, emitidos há no máximo 3 meses
*         Somente são necessários documentos dos sócios administradores

**Atenção! Durante a verificação do cadastro, outros documentos poderão ser solicitados.**

### Você tem alguma dúvida sobre nosso sistema? ###

* Nosso FAQ pode te ajudar: https://www.boletobancario.com/boletofacil/faq/faq.html#emissao;
* Você pode entrar em contato com a gente pelo nosso chat no site;
* Você também pode entrar em contato pelo telefone 41 3013-9650.

== Changelog ==

= 1.2.1 =
Release Date: June 19th, 2018

Bugfixes:
* A validação dos dados do cartão de crédito agora ocorre em tempo real, conforme eles são inseridos pelo cliente;
* Agora, é possível emitir cobranças parceladas com qualquer número de parcelas divididas entre boleto bancário e cartão de crédito - você pode, por exemplo, emitir uma cobrança com 1x parcela no boleto e 5x no cartão;
* Fizemos algumas mudanças nos nossos bastidores pra reforçar ainda mais a segurança dos seus clientes nos pagamentos via cartão de crédito.

== License ==
Boleto Fácil WooCommerce is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

Boleto Fácil WooCommerce is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Boleto Fácil WooCommerce. If not, see <http://www.gnu.org/licenses/>.