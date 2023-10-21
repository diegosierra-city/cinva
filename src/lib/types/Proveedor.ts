export interface Proveedor {
	id: number;
	tipo_documento: number;
	documento: string;
	nombre: string;
	apellido: string;
	nombre_comercial: string;
	pais: string;
	departamento: string;
	ciudad_cod: number;
	ciudad: string;
	telefono: string;
	celular: string;
	direccion: string;
	email: string;
	redes_sociales: string;
	cuenta_bancaria: string;
	banco: string;
	tipo_cuenta: string;
	titular_cuenta: string;
	titular_tipo_documento: number;
	titular_documento: string;
	activo: boolean;
}
