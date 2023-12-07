export interface Pedido {
id: number
comprador_id: number
vendedor_id: number
fecha: any
valor: number
descuento_porcentaje: number
descuento_valor: number
total: number
estado: string //Pendiente-Enviado-Recibido
pago_estado: string //Sin Pago-Pago Parcial -Pagado
pago_id: number
notas: string
fecha_envio: string
origen: string
calificacion: number	
}

export interface PedidoProduct {
 id: number
 order_id: number
 category_id: number
 product_id: number
 name: string
 ref: string
 image: string
 description: string
 description2: string
 size: string
 color: string
 stock: number
 version_type: string
 version: string
 image_version: string
 price: number
 quantity: number
 unit: string
 total: number
 cod: number
}