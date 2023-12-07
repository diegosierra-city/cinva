<script lang="ts">
	import type { Pedido, PedidoProduct } from '$lib/types/Pedido';
	import type { Category } from '$lib/types/Category';
	import type { Product } from '$lib/types/Product';
	import type { User } from '$lib/types/User';
	import type { Proveedor } from '$lib/types/Proveedor';
	import type { Cliente } from '$lib/types/Cliente';
	import { onMount } from 'svelte/internal';
	import { apiKey, userNow } from '../../store';
import ControlPedido from './ControlPedido.svelte';
	import ControlProducto from '$lib/components/ControlProducto.svelte';

	import { formattedNumber } from '$lib/utilities/FormatNumber';
	import foundObject from '$lib/utilities/FoundObject';

	import Messages from '$lib/components/Messages.svelte';
	import type { Message } from '$lib/types/Message';

	const urlAPI = $apiKey.urlAPI;
	let m_show: boolean = false;
	let message: Message;

	function formatearFecha(fecha:Date) {
  var yyyy = fecha.getFullYear();
  var mm = String(fecha.getMonth() + 1).padStart(2, '0'); // Sumar 1 porque los meses van de 0 a 11
  var dd = String(fecha.getDate()).padStart(2, '0');
  return yyyy + '-' + mm + '-' + dd;
}
//
// Obtener la fecha de hoy
var fechaHoy = new Date();
// Obtener la fecha de un mes atrás
var fechaMesAtras = new Date();
fechaMesAtras.setMonth(fechaMesAtras.getMonth() - 1);

	//
	let show_pedido: boolean = false;

	let listProductos: Product[];
	let listCategorias: Category[];
	let listPedidos: Pedido[];
	let listVendedores: User[];
	let listClientes: Cliente[]

	let editPedido: Pedido; //nuevo o editar

	interface Filtro {
		dateIn: any;
		dateOut: any;
	}

	let filtro: Filtro = {
		dateIn: formatearFecha(fechaMesAtras),
		dateOut: formatearFecha(fechaHoy)
	}
	

	const loadProducts = async () => {
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_products&campo=active&campoV=1&orden=category_id,product'
		);
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_products&campo=active&campoV=1&orden=category_id,product'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log("productos:",result);
				listProductos = result;
			})
			.catch((error) => console.log(error));
	};

	const loadCategorias = async () => {
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_categories&campo=active&campoV=1&orden=category'
		);
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_categories&campo=active&campoV=1&orden=category'
		)
			.then((response) => response.json())
			.then((result) => {
				listCategorias = result;
				console.log('Categorias:',listCategorias)
			})
			.catch((error) => console.log(error));
	};

	const loadPedidos = async () => {
		/*console.log(
			urlAPI +
				'?ref=load-list-dates&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_orders&orden=id DESC&date1='+filtro.dateIn+'&date2='+filtro.dateOut
		);
		
		urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_orders'
		*/
		console.log('ORDERs:',
			urlAPI +
				'?ref=load-list-dates&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_orders&orden=id DESC&date1='+filtro.dateIn+'&date2='+filtro.dateOut
		);
		await fetch(
			urlAPI +
				'?ref=load-list-dates&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_orders&orden=id DESC&date1='+filtro.dateIn+'&date2='+filtro.dateOut
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('Pedidos',result)
				listPedidos = result;
			})
			.catch((error) => console.log(error));
	};

const loadClientes = async () => {
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_clientes&orden=nombre'
		);
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_clientes&orden=nombre'
		)
			.then((response) => response.json())
			.then((result) => {
				console.log('Clientes',result)
				listClientes = result;
			})
			.catch((error) => console.log(error));
	}

	const loadVendedores = async () => {
		console.log(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_users&orden=nombre'
		);
		await fetch(
			urlAPI +
				'?ref=load-list&user_id=' +
				$userNow.id +
				'&time_life=' +
				$userNow.user_time_life +
				'&token=' +
				$userNow.token +
				'&folder=cinva_users&orden=nombre'
		)
			.then((response) => response.json())
			.then((result) => {
				//console.log("Listado:");
				listVendedores = result;
			})
			.catch((error) => console.log(error));
	}
	
	const savePedido = async () => {
		await fetch(urlAPI + '?ref=save-list', {
			method: 'POST', //POST - PUT - DELETE
			body: JSON.stringify({
				user_id: $userNow.id,
				time_life: $userNow.user_time_life,
				token: $userNow.token,

				folder: 'cinva_orders',
				request: editPedido
			}),
			headers: {
				'Content-type': 'application/json; charset=UTF-8'
			}
		})
			.then((response) => response.json())
			//.then(result => console.log(result))
			.then((result) => {
				
				message = {
					title: 'Guardar',
					text: 'Se han guardado los datos',
					class: 'message-green',
					accion: ''
				};
				m_show = true;
				//product_list = result;
				show_pedido = false;
			})
			.catch((error) => {
				message = {
					title: 'Error',
					text: 'Error: ' + error,
					class: 'message-red',
					accion: ''
				};
				m_show = true;
			});

	};

	//$: console.log(product_list);

	function addPedido() {
		editPedido = {
id: 0,
comprador_id: 0,
vendedor_id: $userNow.id,
fecha: fechaHoy,
valor: 0,
descuento_porcentaje: 0,
descuento_valor: 0,
total: 0,
estado: 'Pendiente',
pago_estado: 'Sin Pago',
pago_id: 0,
notas: '',
fecha_envio: '',
origen: 'Vendedor',
calificacion: 0,	
		}
		show_pedido= true
	}

function updatePedido(position:number) {
	editPedido	=listPedidos[position]	
	show_pedido= true
}
		

	onMount(async () => {
		await loadProducts()
		await loadCategorias()
		await loadPedidos()
		await loadVendedores()
		await loadClientes()
	});
</script>

<svelte:head>
	<title>Pedidos</title>
</svelte:head>

<div class="px-3 w-full mt-0">
	
	<div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-2">
		<div class="flex">
			<input type="date" class="inputA w-32" bind:value={filtro.dateIn}> <span class="relative top-2">hasta</span> <input type="date" class="inputA w-32" bind:value={filtro.dateOut}> 
   <button class="btn-primary flex bg-green mr-4" on:click={loadPedidos}>
				<i class="fa fa-refresh mr-2 mt-1" />
				</button
			> |  
			<button class="btn-primary flex ml-4" on:click={addPedido}>
				<i class="fa fa-plus mr-2 mt-1" />
				Nuevo Pedido</button
			>
		</div>
		
		{#if listPedidos?.length>0}
					 <!-- content here -->

		<table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
			<thead class="text-xs text-white uppercase bg-primary dark:bg-gray-700 dark:text-gray-400">
				<th scope="col" class="px-4 py-2" />
				<th scope="col" class="px-4 py-2"> Pedido</th>
				<th scope="col" class="px-4 py-2"> Fecha</th>
				<th scope="col" class="px-4 py-2"> Vendedor</th>
				<th scope="col" class="px-4 py-2"> Cliente </th>
				<th scope="col" class="px-4 py-2"> Valor </th>
				<th scope="col" class="px-4 py-2"> Pagado </th>
				<th scope="col" class="px-4 py-2"> Estado </th>
				<th scope="col" class="px-4 py-2" />
			</thead>
			<tbody>
				
						{#each listPedidos as pedido, i}
						<tr class="bg-white border-b hover:bg-aliceblue">
							<td class="text-bold">{i + 1}</td>
							<td>{pedido.id}</td>
							<td>{pedido.fecha}</td>
							<td>{foundObject(listVendedores,'id',pedido.vendedor_id,'nombre')}</td>
							<td>{foundObject(listClientes,'id',pedido.comprador_id,'nombre')}</td>
							<td class="text-right">{pedido.total}</td>
							<td class="text-center">{pedido.pago_estado}</td>
							<td class="text-center">{pedido.estado}: 0%</td>
	
							<td>
								<button
									on:click={() => {
										if (pedido.id > 1000000) {
											message = {
												title: 'Error',
												text: 'Primero haga click en el botón de Guardar productos',
												class: 'message-red',
												accion: ''
											};
											m_show = true;
										} else {
											updatePedido(i)
										}
									}}
								>
									<svg
										xmlns="http://www.w3.org/2000/svg"
										class="h-5 w-5 text-green cursor-pointer hover:black"
										fill="none"
										viewBox="0 0 24 24"
										stroke="currentColor"
										stroke-width="2"
									>
										<path
											stroke-linecap="round"
											stroke-linejoin="round"
											d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
										/>
									</svg>
								</button>
							</td>
							<td>
								
							</td>
						</tr>
	
						<!---->
					
					{/each}
				
			
			</tbody>
		</table>
{:else}
					Sin Pedidos
		{/if}
	</div>
</div>

{#if show_pedido == true}
<ControlPedido bind:m_show bind:message pedido={editPedido} {listClientes} {listCategorias} {listProductos} bind:listPedidos bind:show_pedido  />

{/if}

{#if m_show}
	<!-- content here -->
	<Messages bind:m_show bind:message />
{/if}
