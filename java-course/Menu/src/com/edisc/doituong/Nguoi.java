package com.edisc.doituong;

public class Nguoi {
	String ten;
	String diaChi;
	int tuoi;
	
	public void diLai(String ten) {
		System.out.println(ten + " di lai");
	}

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		new Nguoi(); // doi tuong 1 - object
		Nguoi anhA = new Nguoi(); // doi tuong 2 - object
		Nguoi anhB = new Nguoi(); // doi tuong 3 - object
		
		anhA.diLai("anh A");
		anhB.diLai("anh B");

	}

}
