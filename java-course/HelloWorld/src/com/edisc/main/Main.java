package com.edisc.main;
import com.edisc.hinhhoc.HinhChuNhat;
import com.edisc.hinhhoc.HinhTron;
import com.edisc.hinhhoc.HinhVuong;

public class Main {

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		// tinh chu vi va dien tich cac hinh
		float chieuDai = 3.6f;
		float chieuRong = 1.3f;
		
		float chuViHCN = HinhChuNhat.chuVi(chieuDai, chieuRong);
		float dienTichHCN = HinhChuNhat.dienTich(chieuDai, chieuRong);
		System.out.println("Chu vi va dien tich HCN la");
		System.out.println(chuViHCN);
		System.out.println(dienTichHCN);
		
		float banKinh = 2.5f;
		System.out.println("Chu vi va dien tich Hinh tron la: ");
		System.out.println(HinhTron.chuVi(banKinh));
		System.out.println(HinhTron.dienTich(banKinh));
		
		float canhHinhVuong = 10.4f;
		System.out.println("Chu vi va dien tich Hinh vuong la: ");
		System.out.println(HinhVuong.chuVi(canhHinhVuong));
		System.out.println(HinhVuong.dienTich(canhHinhVuong));
	}

}
	