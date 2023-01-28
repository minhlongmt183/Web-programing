package com.edisc.doituong;

public class Book {
	String ten;
	String moTa;
	double price;
	
	public Book() {
		
	}
	
	public Book(String ten, String moTa) {
		this.ten = ten;
		this.moTa = moTa;
	}
	
	public Book(double price) {
		this.price = price;
	}
	
	public Book(String ten, String moTa, double price) {
		this.ten = ten;
		this.moTa = moTa;
		this.price = price;
	}

	public String getTen() {
		return ten;
	}

	public void setTen(String ten) {
		this.ten = ten;
	}

	public String getMoTa() {
		return moTa;
	}

	public void setMoTa(String moTa) {
		this.moTa = moTa;
	}

	public double getPrice() {
		return price;
	}

	public void setPrice(double price) {
		this.price = price;
	}

}
